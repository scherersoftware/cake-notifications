<?php
namespace Notifications\Model\Table;

use Cake\Core\Configure;
use Cake\I18n\Time;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Validation\Validator;
use Notifications\Model\Entity\Notification;
use Notifications\Transport\Transport;

/**
 * NotificationQueues Model
 */
class NotificationQueueTable extends Table {

/**
 * Initialize method
 *
 * @param array $config The configuration for the Table.
 * @return void
 */
	public function initialize(array $config) {
		$this->table('notification_queue');
		$this->displayField('id');
		$this->primaryKey('id');
		$this->addBehavior('Timestamp');

		$this->entityClass('Notifications\Model\Entity\Notification');

		$this->belongsTo('RecipientUsers', [
			'foreignKey' => 'recipient_user_id',
			'className' => 'Users',
		]);

		$this->schema()->columnType('config', 'json');
	}

/**
 * Default validation rules.
 *
 * @param \Cake\Validation\Validator $validator The validator to use when validating the entity.
 * @return \Cake\Validation\Validator
 */
	public function validationDefault(Validator $validator) {
		$validator
			->add('id', 'valid', ['rule' => 'uuid'])
			->allowEmpty('id', 'create')
			->requirePresence('locale', 'create')
			->notEmpty('locale')
			->add('recipient_user_id', 'valid', ['rule' => 'uuid'])
			->requirePresence('recipient_user_id', 'create')
			->notEmpty('recipient_user_id')
			->allowEmpty('notification_identifier')
			->requirePresence('config', 'create')
			->notEmpty('config')
			->requirePresence('transport', 'create')
			->notEmpty('transport')
			->add('locked', 'valid', ['rule' => 'boolean'])
			->allowEmpty('locked')
			->notEmpty('send_tries')
			->allowEmpty('debug');

		return $validator;
	}

/**
 * Creates a Notification entity
 *
 * @param string $identifier content identifier
 * @param array  $data view vars
 * 					To pass in attachments, add a key named 'attachments' - this array
 * 					will be passed to Email::attachments().
 * @param bool $enqueue whether to immediately save and enqueue the notification
 * @return Notification notification entity
 * @throws \InvalidArgumentException Thrown if a non-existant content identifier is given
 */
	public function createNotification($identifier, array $data, $enqueue = false) {
		$data = Hash::merge([
			'locale' => Configure::read('locale'),
			'recipient_user_id' => null,
			'transport' => null,
			'config' => [],
			'locked' => false,
			'send_tries' => 0,
			'sent' => 0,
			'send_after' => null,
			'notification_identifier' => $identifier
		], $data);

		$content = TableRegistry::get('Notifications.NotificationContents')->getByIdentifier($identifier, $data['locale']);
		if (!$content) {
			throw new \InvalidArgumentException(__d('notifications', 'create_notification.invalid_identifier', $identifier));
		}

		$notification = $this->newEntity($data);
		if ($enqueue) {
			return $this->enqueue($notification);
		}
		return $notification;
	}

/**
 * Creates notifications for multiple notification transports at once. In this case,
 * $data['transport'] should be an array of strings (e.g. ['email', 'push_message'])
 *
 * @param string $identifier content identifier
 * @param array $data view vars and configuration
 * @param bool $enqueue whether to immediately save and enqueue the notifications
 * @return array Array of Notification instances created
 */
	public function createNotifications($identifier, array $data, $enqueue = false) {
		if (!is_array($data['transport'])) {
			$data = [$data['transport']];
		}
		$notifications = [];
		foreach ($data['transport'] as $transport) {
			$notificationData = $data;
			$notificationData['transport'] = $transport;
			$notifications[] = $this->createNotification($identifier, $notificationData, $enqueue);
		}
		return $notifications;
	}

/**
 * Adds a notification to the queue
 *
 * @param Notification $notification notification entity to be enqueued
 * @return mixed false on failure, the Notification entity on success
 */
	public function enqueue(Notification $notification) {
		$ret = $this->save($notification);
		return $ret;
	}

/**
 * Returns a list of queued notifications that need to be sent
 *
 * @param int $size 	Limit of notifications
 * @return array
 */
	public function getBatch($size = 10) {
		$query = $this->find();
		$query->where([
			'locked' => false,
			'send_tries <' => $this->getMaxSendTries(),
			'sent' => false,
			'OR' => [
				'send_after IS' => null,
				'send_after <=' => Time::now()
			]
		]);
		$query->order(['created' => 'ASC']);
		$query->limit($size);

		$batch = $query->all();
		if (!empty($batch)) {
			$ids = [];
			foreach ($batch as $item) {
				$ids[] = $item->id;
			}
			$this->lock($ids);
		}
		return $batch->toArray();
	}

/**
 * Returns the maximum number of send tries for a notification.
 *
 * @return int
 */
	public function getMaxSendTries() {
		return 3;
	}

/**
 * Lock notifications with the given IDs
 *
 * @param array $ids array of notification ids to be locked
 * @return bool
 */
	public function lock(array $ids) {
		return $this->updateAll([
			'locked' => true
		], [
			'id IN' => $ids
		]) > 0;
	}

/**
 * Releases locks of notifications with the given IDs
 *
 * @param array $ids array of notification ids to be locked
 * @return bool
 */
	public function releaseLocks(array $ids) {
		return $this->updateAll([
			'locked' => false
		], [
			'id IN' => $ids
		]);
	}

/**
 * Clears all notification locks
 *
 * @return bool
 */
	public function clearLocks() {
		return $this->updateAll([
			'locked' => false
		], ['1 = 1']);
	}

/**
 * Called when sending the notification failed, increases send tries.
 *
 * @param string $id notification id
 * @return bool
 * @throws RecordNotFoundException
 */
	public function fail($id) {
		$notification = $this->get($id);
		$notification->fail();
		return $this->save($notification) !== false;
	}

/**
 * Called when sending the notification sent succeeded, sets the sent flag
 *
 * @param string $id notification id
 * @return bool
 * @throws RecordNotFoundException
 */
	public function success($id) {
		$notification = $this->get($id);
		$notification->success();
		return $this->save($notification) !== false;
	}

/**
 * Send the notification
 *
 * @param Notification $notification notification entity to be sent
 * @param array $transportConfig optional overriding of transport config
 * @return mixed
 */
	public function send(Notification $notification, array $transportConfig = []) {
		$transport = Transport::factory($notification->transport, $transportConfig);
		$user = $this->RecipientUsers->get($notification->recipient_user_id);
		$model = TableRegistry::get('Notifications.NotificationContents');
		$content = $model->getByIdentifier($notification->notification_identifier, $notification->locale);

		return $transport->sendNotification($user, $notification, $content);
	}
}

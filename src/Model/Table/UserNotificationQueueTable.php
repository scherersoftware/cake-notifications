<?php
namespace Notifications\Model\Table;

use Cake\Core\Configure;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use Cake\Validation\Validator;
use Notifications\Model\Entity\UserNotificationQueue;

/**
 * UserNotificationQueue Model
 */
class UserNotificationQueueTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('user_notification_queue');

        $this->displayField('id');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');

        $this->entityClass('Notifications\Model\Entity\UserNotification');

        $this->belongsTo('RecipientUsers', [
            'foreignKey' => 'recipient_user_id',
            'className' => 'Users',
        ]);

        $this->schema()->columnType('config', 'json');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->add('id', 'valid', ['rule' => 'uuid'])
            ->allowEmpty('id', 'create')

            ->requirePresence('locale', 'create')
            ->notEmpty('locale')

            ->allowEmpty('notification_identifier')

            ->allowEmpty('config')

            ->add('seen', 'valid', ['rule' => 'boolean'])
            ->requirePresence('seen', 'create')
            ->notEmpty('seen')

            ->allowEmpty('model')

            ->add('foreign_key', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('foreign_key');

        return $validator;
    }

    /**
     * create a notification and save it to the queue, optionally also create an email
     *
     * @param  string $identifier the notification identifier
     * @param  array  $data       view vars and configuration
     * @param  bool   $asEmail    whether to send an email additionally
     * @return UserNotification
     */
    public function createNotification($identifier, array $data, $asEmail = false) {
        $data = Hash::merge([
            'locale' => Configure::read('locale'),
            'recipient_user_id' => [],
            'model' => null,
            'foreign_key' => null,
            'config' => [],
            'seen' => 0,
            'notification_identifier' => $identifier
        ], $data);

        if (empty($data['recipient_user_id'])) {
            throw new \InvalidArgumentException(__d('notifications', 'create_notification.no_recipient_user_given'));
        }

        $content = TableRegistry::get('Notifications.UserNotificationContents')->getByIdentifier($identifier, $data['locale']);
        if (!$content) {
            throw new \InvalidArgumentException(__d('notifications', 'create_notification.invalid_identifier', $identifier));
        }

        $userNotification = $this->newEntity($data);
        if (!$this->save($userNotification)) {
            debug($userNotification);exit;
        }

        if ($asEmail) {
            $NotificationQueue = TableRegistry::get('Notifications.NotificationQueue');
            $data['config']['link'] = Router::url($data['config']['link'], true);
            if (is_array($data['recipient_user_id'])) {
                foreach ($data['recipient_user_id'] as $recipientUserId) {
                    $emailData = [
                        'recipient_user_id' => $recipientUserId,
                        'transport' => 'email',
                        'config' => $data['config'],
                    ];
                    $NotificationQueue->createNotification($identifier, $emailData, true);
                }
            } else {
                $emailData = [
                    'recipient_user_id' => $data['recipient_user_id'],
                    'transport' => 'email',
                    'config' => $data['config'],
                ];
                $NotificationQueue->createNotification($identifier, $emailData, true);
            }
        }
        return $userNotification;
    }

    /**
     * Creates notifications for multiple recipient users at once. In this case,
     * $data['recipient_user_id'] should be an array of ids
     *
     * @param string $identifier content identifier
     * @param array $data view vars and configuration
     * @param bool $asEmail whether to send emails additionally to the user notification
     * @return array Array of Notification instances created
     */
    public function createNotifications($identifier, array $data, $asEmail = false) {
        if (!is_array($data['recipient_user_id'])) {
            $data = [$data['recipient_user_id']];
        }
        $notifications = [];
        foreach ($data['recipient_user_id'] as $userId) {
            $notificationData = $data;
            $notificationData['recipient_user_id'] = $userId;
            $notifications[] = $this->createNotification($identifier, $notificationData, $asEmail);
        }
        return $notifications;
    }

    /**
     * get all notifications for one user with options for unread only, count only and pagination parameters
     *
     * @param  int $userId     the user identifier
     * @param  bool   $unreadOnly if only unseen notifications
     * @param  bool   $countOnly  only the count of the found notifications
     * @param  int    $limit      limit for pagination
     * @param  int    $page       page for pagination
     * @return array
     */
    public function getNotificationsForUser($userId, $unreadOnly = false, $countOnly = false, $limit = 5, $page = 1)
    {
        $query = $this->find()
            ->where(['recipient_user_id' => $userId])
            ->contain('RecipientUsers')
            ->order(['UserNotificationQueue.created' => 'DESC'])
            ->limit($limit)
            ->page($page);

        if ($unreadOnly) {
            $query->where(['seen' => false]);
        }
        if ($countOnly) {
            return $query->count();
        }

        $notifications = $query->toArray();

        foreach ($notifications as $key => $notification) {
            $notifications[$key]->content = TableRegistry::get('Notifications.UserNotificationContents')->getByIdentifier($notification->notification_identifier, $notification->locale)->render('notification', $notification);
        }

        $notifications['moreEntriesAvailable'] = ($query->count() > ($limit * $page)) ? true : false;

        return $notifications;
    }

    /**
     * Marks the notification as seen/read
     *
     * @param string $id notification id
     * @return bool
     * @throws RecordNotFoundException
     */
    public function read($id) {
        $notification = $this->get($id);
        $notification->read();
        return $this->save($notification) !== false;
    }

    /**
     * get the count of unread user notifications for one entity (project for example) and one user
     *
     * @param  string $model      model
     * @param  int $foreignKey foreign key
     * @param  int $userId     user id
     * @return int
     */
    public function getUnreadCountForEntity($model, $foreignKey, $userId)
    {
        return $this->find()
            ->where([
                'model' => $model,
                'foreign_key' => $foreignKey,
                'recipient_user_id' => $userId,
                'seen' => false
            ])
            ->count();
    }
}

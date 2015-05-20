<?php
namespace Notifications\Model\Table;

use Cake\Core\Configure;
use Cake\I18n\Time;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Validation\Validator;

/**
 * UserNotifications Model
 */
class UserNotificationsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config) {
        $this->table('user_notifications');
        $this->displayField('id');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');

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
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('id', 'create')
            ->requirePresence('locale', 'create')
            ->notEmpty('locale')
            ->add('recipient_user_id', 'valid', ['rule' => 'numeric'])
            ->requirePresence('recipient_user_id', 'create')
            ->notEmpty('recipient_user_id')
            ->allowEmpty('notification_identifier')
            ->requirePresence('config', 'create')
            ->notEmpty('config')
            ->allowEmpty('model')
            ->add('foreign_key', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('foreign_key');

        return $validator;
    }

    public function createNotification($identifier, array $data, bool $asEmail = false) {
        $data = Hash::merge([
            'locale' => Configure::read('locale'),
            'recipient_user_id' => [],
            'model' => null,
            'foreign_key' => null,
            'config' => [],
            'read' => 0,
            'notification_identifier' => $identifier
        ], $data);

        $content = TableRegistry::get('Notifications.NotificationContents')->getByIdentifier($identifier, $data['locale']);
        if (!$content) {
            throw new \InvalidArgumentException(__d('notifications', 'create_notification.invalid_identifier', $identifier));
        }

        if ($asEmail) {
            $NotificationQueue = TableRegistry::get('Notifications.NotificationQueue');
            foreach ($data['recipient_user_id'] as $recipientUserId) {
                $emailData = [
                    'recipient_user_id' => $recipientUserId,
                    'transport' => 'email',
                ];
                $NotificationQueue->createNotification($identifier, $emailData, true);
            }
        }

        $userNotification = $this->newEntity($data);
        return $notification;
    }


    public function getNotificationsForUser($userId, $unreadOnly = false)
    {
        $query = $this->find()
            ->where(['UserNotifications.recipient_user_id' => $userId]);

        if ($unreadOnly) {
            $query->where(['UserNotifications.read' => false]);
        }

        return $query->toArray();
    }

}

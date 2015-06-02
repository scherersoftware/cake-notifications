<?php
namespace Notifications\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * UserNotifications Model
 */
class UserNotificationContentsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config) {
        $this->table('user_notification_contents');
        $this->displayField('id');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');

        $this->addBehavior('Translate', ['fields' => [
            'notification'
        ]]);

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

    /**
     * Find a notification by its string identifier
     *
     * @param string $identifier identifier to look for
     * @param string $locale will override the locale of TranslateBehavior temporarily
     * @return Notification
     */
    public function getByIdentifier($identifier, $locale = null) {
        if ($locale) {
            $oldLocale = $this->locale();
            $this->locale($locale);
        }

        $res = $this->find()->where([
            'UserNotificationContents.notification_identifier' => $identifier
        ])->first();

        if ($locale) {
            $this->locale($oldLocale);
        }

        return $res;
    }

}

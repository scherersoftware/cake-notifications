<?php
namespace Notifications\Model\Entity;

use Cake\ORM\Entity;

/**
 * UserNotificationQueue Entity.
 */
class UserNotification extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'locale' => true,
        'recipient_user_id' => true,
        'notification_identifier' => true,
        'config' => true,
        'seen' => true,
        'model' => true,
        'foreign_key' => true,
        'recipient_user' => true,
        'created' => true
    ];

    /**
     * Marks the notification as seen
     *
     * @return void
     */
    public function read() {
        $this->seen = true;
    }
}

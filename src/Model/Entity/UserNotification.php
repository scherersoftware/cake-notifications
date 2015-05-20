<?php
namespace Notifications\Model\Entity;

use Cake\ORM\Entity;

/**
 * UserNotification Entity.
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
        'model' => true,
        'foreign_key' => true,
        'read' => true,
        'recipient_user' => true,
    ];

    /**
     * Marks the notification as read
     *
     * @return void
     */
    public function read() {
        $this->read = true;
    }
}

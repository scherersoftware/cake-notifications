<?php
namespace Notifications\Model\Entity;

use Cake\ORM\Entity;
use Cake\Utility\Text;

/**
 * UserNotification Entity.
 */
class UserNotificationContent extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'notification_identifier' => true,
        'notes' => true,
        'notification' => true
    ];

    /**
     * Render a field by replacing the placeholders
     *
     * @param string $field field name
     * @param Notification $notification notification containing the view vars
     * @return string
     */
    public function render($field, UserNotification $notification) {
        return Text::insert($this->get($field), $notification->config, [
            'before' => '{{',
            'after' => '}}',
        ]);
    }
}

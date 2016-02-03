<?php
namespace Notifications\Transport;

use App\Model\Entity\User;
use Cake\Core\Configure;
use Notifications\Model\Entity\Notification;
use Notifications\Model\Entity\NotificationContent;
use Parse\ParseClient;
use Parse\ParseInstallation;
use Parse\ParsePush;

class PushMessageTransport extends Transport {

/**
 * Creates a Transport instance
 *
 * @param array $config transport-specific configuration options
 */
    public function __construct(array $config) {
        parent::__construct($config);
        $keys = Configure::read('Notifications.transports.push_message');
        if (Configure::check('Notifications.transports.push_message.' . ENVIRONMENT)) {
            // prefer environment specific config keys
            $keys = Configure::read('Notifications.transports.push_message.' . ENVIRONMENT);
        }
        ParseClient::initialize( $keys['app_id'], $keys['rest_key'], $keys['master_key'] );
    }

/**
 * Abstract sender method
 *
 * @param User $user The recipient user
 * @param Notification $notification the notification to be sent
 * @param NotificationContent $content the content
 * @return mixed
 */
    public function sendNotification(User $user, Notification $notification, NotificationContent $content) {
        $query = ParseInstallation::query();
        $query->equalTo('user_id', $user->id);
        $alert = $content->render('push_message', $notification);
        if (empty($alert) && !empty($notification->config['content_fallback_transport'])) {
            $alert = $content->render($notification->config['content_fallback_transport'], $notification);
        }
        $data = [
            'alert' => $alert,
            'badge' => 'Increment'
        ];
        $result = ParsePush::send(array(
            'where' => $query,
            'data' => $data
        ));
        return is_array($result) && isset($result['result']) && $result['result'];
    }
}

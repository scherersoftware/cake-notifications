<?php
namespace Notifications\Transport;

use App\Model\Entity\User;
use Cake\Core\Configure;
use Cake\Network\Email\Email;
use Cake\Utility\Hash;
use Notifications\Model\Entity\Notification;
use Notifications\Model\Entity\NotificationContent;

class EmailTransport extends Transport {

/**
 * Creates a Transport instance
 *
 * @param array $config transport-specific configuration options
 */
    public function __construct(array $config) {
        $config = Hash::merge(Configure::read('Notifications.transports.email'), $config);
        $config = Hash::merge([
            'profile' => 'default',
            'emailTransport' => 'default'
        ], $config);
        parent::__construct($config);
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
        $subject = $content->render('email_subject', $notification);
        $htmlBody = $content->render('email_html', $notification);
        $textBody = $content->render('email_text', $notification);
        $email = new Email($this->_config['profile']);
        $email->transport($this->_config['emailTransport']);
        $email->emailFormat('html');

        if (!empty($notification->config['attachments'])) {
            $email->attachments($notification->config['attachments']);
        }

        $email->to([ $user->email => $user->firstname . ' ' . $user->lastname ]);
        $email->subject($subject);

        if (!empty($this->_config['templated']) && !empty($this->_config['template']) && !empty($this->_config['layout'])) {
            $email->template($this->_config['template'], $this->_config['layout']);
            $email->viewVars(['content' => $htmlBody]);
            return $email->send();
        }
        return $email->send($htmlBody);
    }
}

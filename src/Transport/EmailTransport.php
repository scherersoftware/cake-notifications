<?php 
namespace Notifications\Transport;

use App\Model\Entity\User;
use Cake\Network\Email\Email;
use Notifications\Model\Entity\Notification;
use Notifications\Model\Entity\NotificationContent;

class EmailTransport extends Transport {

/**
 * @var array
 */
	protected $_config = [
		'profile' => 'default',
		'emailTransport' => 'default'
	];

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
		return $email->send($htmlBody);
	}
}
<?php
namespace Notifications\Transport;

use App\Model\Entity\User;
use Notifications\Model\Entity\Notification;
use Notifications\Model\Entity\NotificationContent;

class SmsTransport extends Transport {

/**
 * Abstract sender method
 *
 * @param User $user The recipient user
 * @param Notification $notification the notification to be sent
 * @param NotificationContent $content the content
 * @return mixed
 */
	public function sendNotification(User $user, Notification $notification, NotificationContent $content) {
	}
}

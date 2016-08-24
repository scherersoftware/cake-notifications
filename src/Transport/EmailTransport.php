<?php
namespace Notifications\Transport;

use Cake\Core\Configure;
use Cake\I18n\I18n;
use Cake\Mailer\Email;
use josegonzalez\Queuesadilla\Job\Base;
use Notifications\Notification\EmailNotification;
use Notifications\Notification\Notification;
use Notifications\Transport\TransportInterface;

class EmailTransport extends Transport implements TransportInterface
{

    /**
     * Send function
     *
     * @param Notification $notification Notification object
     * @param string|array|null $content String with message or array with messages
     * @return void
     */
    public static function sendNotification(Notification $notification, $content = null)
    {
        $beforeSendCallback = $notification->beforeSendCallback();
        self::_performCallback($beforeSendCallback, $notification);

        if ($notification->locale() !== null) {
            I18n::locale($notification->locale());
        } else {
            I18n::locale(Configure::read('Notifications.defaultLocale'));
        }

        $notification->email()->send($content);

        $afterSendCallback = $notification->afterSendCallback();
        self::_performCallback($afterSendCallback);
    }

    /**
     * Process the job coming frim the Queue
     *
     * @param Base $job Queuesadilla base job
     * @return void
     */
    public static function processQueueObject(Base $job)
    {
        $notification = new EmailNotification();

        if ($job->data('beforeSendCallback') !== []) {
            $notification->beforeSendCallback($job->data('beforeSendCallback')['class'], $job->data('beforeSendCallback')['args']);
        }
        if ($job->data('afterSendCallback') !== []) {
            $notification->afterSendCallback($job->data('afterSendCallback')['class'], $job->data('afterSendCallback')['args']);
        }
        if ($job->data('locale') !== '') {
            $notification->locale($job->data('locale'));
        }
        $notification->unserialize($job->data('email'));
        self::sendNotification($notification);
    }
}

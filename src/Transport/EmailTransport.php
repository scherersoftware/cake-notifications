<?php
namespace Notifications\Transport;

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
        self::_performCallback($beforeSendCallback);

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
            $notification->beforeSendCallback($job->data('beforeSendCallback'));
        }
        if ($job->data('afterSendCallback') !== []) {
            $notification->afterSendCallback($job->data('beforeSendCallback'));
        }
        $notification->unserialize($job->data('email'));

        self::sendNotification($notification);
    }
}

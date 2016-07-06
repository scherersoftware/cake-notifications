<?php
namespace Notifications\Transport;

use Cake\Mailer\Email;
use josegonzalez\Queuesadilla\Job\Base;
use Notifications\Notification\EmailNotification;
use Notifications\Notification\Notification;

class EmailTransport extends Transport
{

    /**
     * Send function
     *
     * @param obj $notification EmailNotification
     * @return void
     */
    public static function sendNotification(EmailNotification $notification)
    {
        $beforeSendCallback = $notification->beforeSendCallback();
        self::_performCallback($beforeSendCallback);

        $notification->email()->send();

        $afterSendCallback = $notification->afterSendCallback();
        self::_performCallback($afterSendCallback);
    }

    /**
     * Process the job coming frim the Queue
     *
     * @param Base $job Queuesadilla base job
     * @return void
     */
    public static function processQueueObject(Base $job) {
        $notification = new EmailNotification();

        if($job->data('beforeSendCallback') !== []) {
            $notification->beforeSendCallback($job->data('beforeSendCallback'));
        }
        if($job->data('afterSendCallback') !== []) {
            $notification->afterSendCallback($job->data('beforeSendCallback'));
        }
        $notification->unserialize($job->data('email'));

        self::sendNotification($notification);
    }
}

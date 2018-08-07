<?php
namespace Notifications\Transport;

use Cake\Core\Configure;
use Cake\I18n\I18n;
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
     * @return \Notifications\Notification\Notification
     */
    public static function sendNotification(Notification $notification, $content = null)
    {
        $beforeSendCallback = $notification->beforeSendCallback();
        self::_performCallback($beforeSendCallback, $notification);

        if ($notification->locale() !== null) {
            I18n::setLocale($notification->locale());
        } else {
            I18n::setLocale(Configure::read('Notifications.defaultLocale'));
        }

        $notification->email()->send($content);

        $afterSendCallback = $notification->afterSendCallback();
        self::_performCallback($afterSendCallback);

        return $notification;
    }

    /**
     * Process the job coming from the queue
     *
     * @param Base $job Queuesadilla base job
     * @return \Notifications\Notification\Notification
     */
    public static function processQueueObject(Base $job)
    {
        $notification = new EmailNotification();

        if ($job->data('beforeSendCallback') !== []) {
            foreach ($job->data('beforeSendCallback') as $callback) {
                $notification->addBeforeSendCallback($callback['class'], $callback['args']);
            }
        }
        if ($job->data('afterSendCallback') !== []) {
            foreach ($job->data('afterSendCallback') as $callback) {
                $notification->addAfterSendCallback($callback['class'], $callback['args']);
            }
        }
        if ($job->data('locale') !== '') {
            $notification->locale($job->data('locale'));
        }
        $notification->unserialize($job->data('email'));

        return self::sendNotification($notification);
    }
}

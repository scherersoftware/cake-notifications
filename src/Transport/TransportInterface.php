<?php
namespace Notifications\Transport;

use Notifications\Notification\Notification;

interface TransportInterface
{

    /**
     * sendNotification method
     *
     * @param Notification $notification Notification object
     * @param string|array|null $content String with message or array with messages
     * @return void
     */
    public static function sendNotification(Notification $notification, $content = null);
}

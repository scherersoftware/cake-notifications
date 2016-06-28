<?php
namespace Notifications\Notification;

class NotificationFactory
{
    public static function create($type, array $config = []) {
        $map = [
            'email' => 'Notifications\Notification\EmailNotification'
        ];
        if (!isset($map[$type])) {
            throw new \InvalidArgumentException("{$type} is not a valid notification");
        }
        $className = $map[$type];
        $notification = new $className($config);
        return $notification;
    }
}

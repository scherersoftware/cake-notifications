<?php
namespace Notifications\Transport;

use Notifications\Notification\Notification;

abstract class Transport
{

    /**
     * Abstract sendNotification method
     *
     * @param Notification $notification Notification object
     * @param string|array|null $content String with message or array with messages
     * @return void
     */
    abstract public static function sendNotification(Notification $notification, $content = null);

    /**
     * Performs the before- or after send callback of the notification
     *
     * @param array $item Contains the class and function name and optional, function params
     * @return bool
     */
    protected static function _performCallback($item)
    {
        if (!isset($item['class']) || !is_callable($item['class'])) {
            return false;
        }

        $args = [];
        if (isset($item['args']) && is_array($item['args'])) {
            $args = $item['args'];
        }

        $success = false;
        if (is_array($item['class']) && count($item['class']) == 2) {
            $className = $item['class'][0];
            $methodName = $item['class'][1];
            $instance = new $className;
            $success = call_user_func([$instance, $methodName], $args);
        } elseif (is_string($item['class'])) {
            $success = call_user_func($item['class'], $args);
        }
        if ($success !== false) {
            $success = true;
        }

        return $success;
    }
}

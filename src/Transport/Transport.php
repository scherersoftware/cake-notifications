<?php
namespace Notifications\Transport;

abstract class Transport
{
    /**
     * Abstract sender method
     *
     */
    abstract public function sendNotification($notification);

    /**
     * Performs the before- or after send callback of the notification
     *
     * @param array $item Contains the class and function name and optional, function params
     * @return bool
     */
    protected function _performCallback($item)
    {
        if (!isset($item['class']) || !is_callable($item['class'])) {
            return false;
        }

        $args = [];
        if (isset($item['args']) && is_array($item['args'])) {
            $args = $item['args'];
        }

        $success = false;

        $success = call_user_func_array($item['class'], $args);
        if ($success !== false) {
            $success = true;
        }

        return $success;
    }
}

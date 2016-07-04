<?php
namespace Notifications\Transport;

abstract class Transport
{

    /**
     * An array mapping transports to their classes
     *
     * @var array
     */
    private static $__transports = [
        'email' => 'Notifications\Notification\EmailNotification',
        'sms' => 'Notifications\Notification\SmsNotification'
    ];

    /**
     * Abstract sender method
     *
     */
    abstract public function sendNotification($notification);

    /**
     * Creates a Transport instance based on the given type
     *
     * @param string $type email/sms
     * @return Transport
     * @throws \InvalidArgumentException
     */
    public static function factory($type)
    {
        if (!isset($this->__transports[$type])) {
            throw new \InvalidArgumentException("{$type} is not a valid transport");
        }
        $className = $this->__transports[$type];
        return $transport;
    }

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

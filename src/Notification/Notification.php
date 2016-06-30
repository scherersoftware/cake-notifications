<?php
namespace Notifications\Notification;

abstract class Notification
{
    /**
     * Before send callback.
     *
     * @var array
     */
    protected $_beforeSendCallback = [];

    /**
     * After send callback.
     *
     * @var array
     */
    protected $_afterSendCallback = [];

    /**
     * Settings
     *
     * @var array
     */
    protected $_settings = [];

    /**
     * An array mapping notifications to their classes
     *
     * @var array
     */
    private static $__notifications = [
        'email' => 'Notifications\Notification\EmailNotification',
        'sms' => 'Notifications\Notification\SmsNotification'
    ];

    /**
     * Push the Notification into the queue
     *
     * @return bool
    */
    abstract public function push();

    /**
     * Get/Set Before send callback.
     *
     * @param array|null
     * @return array
    */
    public function beforeSendCallback($class = null, $args = null)
    {
        if ($class === null) {
            return $this->_beforeSendCallback;
        }
        return $this->__setCallback('_beforeSendCallback', $class, $args);
    }

    /**
     * Get/Set After send callback.
     *
     * @param array|null
     * @return array
    */
    public function afterSendCallback($class = null, $args = null)
    {
        if ($class === null) {
            return $this->_afterSendCallback;
        }
        return $this->__setCallback('_afterSendCallback', $class, $args);
    }

    /**
     * Get/Set Settings.
     *
     * ### Supported settings
     *
     * - attempts: how often the notification will be executed again after failure
     * - attempts_delay: how long it takes in seconds until the notification will be executed again
     * - delay: how long it takes until the notification will be executed for the first time  in seconds
     * - expires_in: how long the notification will stay in the queue in seconds
     * - queue: name of the queue
     *
     * @param array|null
     * @return array
    */
    public function settings(array $settings = null)
    {
        if ($settings === null) {
            return $this->_settings;
        }
        return $this->__setSettings($settings);
    }

    /**
     * Return an instance of the requested notification
     *
     * @param string $type 
     * @param array $config 
     * @return Notification
     */
    public static function factory($type, array $config = []) {
        if (!isset(self::$__notifications)) {
            throw new \InvalidArgumentException("{$type} is not a valid notification");
        }
        $className = self::$__notifications[$type];
        $notification = new $className($config);
        return $notification;
    }

    /**
     * Set settings
     *
     * @return $this
     */
    private function __setSettings($settings)
    {
        $this->_settings = $settings;
        return $this;
    }

    /**
     * Set callback
     *
     * @return $this
     */
    private function __setCallback($type, $class, $args)
    {
        if (!is_array($class)) {
            $this->{$type} = [
                'class' => $class,
                'args' => $args
            ];
            return $this;
        } else if (is_array($class) && count($class) == 2) {
            $className = $class[0];
            $methodName = $class[1];
        } else {
            throw new \InvalidArgumentException("{$class} is missformated");
        }

        $this->{$type} = [
            'class' => [$className, $methodName],
            'args' => $args
        ];
        return $this;
    }
}

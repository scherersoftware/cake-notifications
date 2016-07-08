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
     * Queue options
     *
     * @var array
     */
    protected $_queueOptions = [];

    /**
     * Push the Notification into the queue
     *
     * @return bool
    */
    abstract public function push();

    /**
     * Send the Notification immediately
     *
     * @param string|array|null $content String with message or array with messages
     * @return void
     */
    abstract public function send($content = null);

    /**
     * Get/Set Before send callback.
     *
     * @param array|null
     * @return array
    */
    public function beforeSendCallback($class = null, array $args = [])
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
    public function afterSendCallback($class = null, array $args = [])
    {
        if ($class === null) {
            return $this->_afterSendCallback;
        }
        return $this->__setCallback('_afterSendCallback', $class, $args);
    }

    /**
     * Get/Set Queue Optons.
     *
     * ### Supported options
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
    public function queueOptions(array $options = null)
    {
        if ($options === null) {
            return $this->_queueOptions;
        }
        return $this->__setQueueOptions($options);
    }

    /**
     * Set settings
     *
     * @param array
     * @return $this
     */
    private function __setQueueOptions($options)
    {
        $this->_queueOptions = $options;
        return $this;
    }

    /**
     * Set callback
     *
     * @param string $type _beforeSendCallback or _afterSendCallback
     * @param string $class name of the class
     * @param array $args array of arguments
     * @return $this
     */
    private function __setCallback($type, $class, array $args)
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
            if (is_array($class)) {
                $class = implode($class);
            }
            throw new \InvalidArgumentException("{$class} is missformated");
        }

        $this->{$type} = [
            'class' => [$className, $methodName],
            'args' => $args
        ];
        return $this;
    }
}

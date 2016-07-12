<?php
namespace Notifications\Notification;

abstract class Notification implements NotificationInterface
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
     * {@inheritdoc}
     */
    public function beforeSendCallback($class = null, array $args = [])
    {
        if ($class === null) {
            return $this->_beforeSendCallback;
        }
        return $this->__setCallback('_beforeSendCallback', $class, $args);
    }

    /**
     * {@inheritdoc}
     */
    public function afterSendCallback($class = null, array $args = [])
    {
        if ($class === null) {
            return $this->_afterSendCallback;
        }
        return $this->__setCallback('_afterSendCallback', $class, $args);
    }

    /**
     * {@inheritdoc}
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
     * @param array $options Queue options
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
        } elseif (is_array($class) && count($class) == 2) {
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

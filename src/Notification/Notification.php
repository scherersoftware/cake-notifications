<?php
namespace Notifications\Notification;

use Cake\Core\Configure;

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
     * Locale string
     *
     * @var string
     */
    protected $_locale = null;

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
     * Constructor
     *
     * @throws InvalidArgumentException
     */
    public function __construct($config = null)
    {
        if (Configure::read('Notifications.defaultLocale') === null) {
            throw new \InvalidArgumentException("Notifications.defaultLocale is not configured");
        }
    }

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
     * {@inheritdoc}
     */
    public function locale($locale = null)
    {
        if ($locale === null) {
            return $this->_locale;
        }
        return $this->__setLocale($locale);
    }

    /**
     * Set lcoale
     *
     * @param string $locale locale - must be i18n conform
     * @return $this
     */
    private function __setLocale($locale)
    {
        $this->_locale = $locale;
        return $this;
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

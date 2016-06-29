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
     * Queue
     *
     * @var string
     */
    protected $_queue = '';

    /**
     * Constructor
     * 
     */
    abstract public function __construct();

    /**
     * Get/Set Before send callback.
     *
     * @param array|null
     * @return array
    */
    public function beforeSendCallback($class = null, $args = null)
    {
    }

    /**
     * Get/Set After send callback.
     *
     * @param array|null
     * @return array
    */
    public function afterSendCallback($afterSendCallback = null)
    {
    }

    /**
     * Get/Set Queue.
     *
     * @param string|null
     * @return string
    */
    public function queue($queue = null)
    {
    }

    /**
     * Push the Notification into the queue
     *
     * @return bool
    */
    public function push()
    {
    }

    /**
     * Reset all the internal variables to be able to send out a notification
     *
     * @return $this
     */
    public function reset()
    {
        $this->_beforeSendCallback = [];
        $this->_afterSendCallback = [];
        $this->_queue = [];
    }

    /**
     * Return an instnace of the requested notification
     *
     * @param string $type 
     * @param array $config 
     * @return void
     */
    public static function factory($type, array $config = []) {
        $map = [
            'email' => 'Notifications\Notification\EmailNotification',
            'sms' => 'Notifications\Notification\SmsNotification'
        ];
        if (!isset($map[$type])) {
            throw new \InvalidArgumentException("{$type} is not a valid notification");
        }
        $className = $map[$type];
        $notification = new $className($config);
        return $notification;
    }

    /**
     * undocumented function
     *
     * @return void
     */
    private function __setCallback($type, $class, $args)
    {
    }
}

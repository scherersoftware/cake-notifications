<?php
namespace Notifications\Notification;

use Cake\Mailer\Email;

class EmailNotification implements NotificationInterface
{
    /**
     * Cake Email object
     *
     * @var obj
     */
    protected $_email;
    
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
     * Queue settings
     *
     * @var array
     */
    protected $_queue = [];

    /**
     * Constructor.
     * 
     */
    public function __construct(array $config = [])
    {
        $this->_email = new Email($config);
    }

    /**
     * Overload Cake\Mailer\mail functions
     *
     * @param string $name
     * @param string $args
     * @return this
     */
    public function __call($name, $args)
    {
        call_user_func_array([$this->_email, $name], $args);
        return $this;
    }

    /**
     * Overload Cake\Mailer\mail functions
     *
     * @param string $name
     * @param string $args
     * @return this
     */
    public static function __callStatic($name, $args)
    {
        call_user_func_array([$this->_email, $name], $args);
        return $this;
    }

    /**
     * Get/Set Before send callback.
     *
     * @param array|null
     * @return array
    */
    public function beforeSendCallback($beforeSendCallback = null)
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
     * Get/Set Queue settings.
     *
     * @param array|null
     * @return array
    */
    public function queue($queue = null)
    {
    }

    /**
     * Push the Email into the queue
     *
     * @return bool
    */
    public function push()
    {
    }

    /**
     * Reset all the internal variables to be able to send out a new email.
     *
     * @return $this
     */
    public function reset()
    {
        $this->_beforeSendCallback = [];
        $this->_afterSendCallback = [];
        $this->_queue = [];

        return $this;
    }

}
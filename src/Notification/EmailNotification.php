<?php
namespace Notifications\Notification;

use Cake\Mailer\Email;
use Notifications\Notification\Notification;
use Josegonzalez\CakeQueuesadilla\Queue\Queue;

class EmailNotification extends Notification implements NotificationInterface
{

    /**
     * Transport class
     *
     * @var string
     */
    protected $_transport = '\Notifications\Transport\EmailTransport';

    /**
     * Cake Email object
     *
     * @var obj
     */
    protected $_email;

    /**
     * Constructor
     * 
     */
    public function __construct(array $config = [])
    {
        $this->_email = new Email($config);
    }

    /**
     * Push the EmailNotification into the queue
     *
     * @return bool
    */
    public function push()
    {
        return Queue::push([
            $this->_transport, 'sendNotification'
        ], [
            'email' => serialize($this->_email)
        ], $this->_settings);
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

}
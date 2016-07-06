<?php
namespace Notifications\Notification;

use Cake\Mailer\Email;
use Notifications\Notification\Notification;
use Notifications\Transport\EmailTransport;
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
    public function __construct($config = null)
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
            $this->_transport, 'processQueueObject'
        ], [
            'email' => $this->_email->serialize(),
            'beforeSendCallback' => $this->_beforeSendCallback,
            'afterSendCallback' => $this->_afterSendCallback
        ], $this->_queueOptions);
    }

    /**
     * Send the EmailNotification immediately using the correspondending transport class
     *
     * @param string|array|null $content String with message or array with messages
     * @return bool
    */
    public function send($content = null)
    {
        return EmailTransport::sendNotification($this, $content);
    }

    /**
     * Get the Cake Email object
     *
     * @return obj Email
    */
    public function email()
    {
        return $this->_email;
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
        forward_static_call(['Cake\Mailer\Email', $name], $args);
    }

}
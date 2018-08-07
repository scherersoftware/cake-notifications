<?php
namespace Notifications\Notification;

use Cake\Mailer\Email;
use Josegonzalez\CakeQueuesadilla\Queue\Queue;
use Notifications\Notification\Notification;
use Notifications\Transport\EmailTransport;

class EmailNotification extends Notification
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
     * @var object
     */
    protected $_email;

    /**
     * Constructor
     *
     * @param array|null $config
     */
    public function __construct($config = null)
    {
        parent::__construct();
        $this->_email = new Email($config);
    }

    /**
     * {@inheritdoc}
     */
    public function push()
    {
        return Queue::push($this->_transport . '::processQueueObject', [
            'email' => $this->_email->serialize(),
            'beforeSendCallback' => $this->_beforeSendCallback,
            'afterSendCallback' => $this->_afterSendCallback,
            'locale' => $this->_locale
        ], $this->_queueOptions);
    }

    /**
     * Send the EmailNotification immediately using the corresponding transport class
     *
     * @param string|array|null $content String with message or array with messages
     * @return \Notifications\Notification\Notification
     */
    public function send($content = null)
    {
        return EmailTransport::sendNotification($this, $content);
    }

    /**
     * Get the Cake Email object
     *
     * @return object Email
     */
    public function email()
    {
        return $this->_email;
    }

    /**
     * Overload Cake\Mailer\mail functions
     *
     * @param string $name method name
     * @param array  $args arguments
     * @return \Notifications\Notification\EmailNotification
     */
    public function __call($name, $args)
    {
        call_user_func_array([$this->_email, $name], $args);

        return $this;
    }
}

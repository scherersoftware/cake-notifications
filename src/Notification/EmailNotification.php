<?php
namespace Notifications\Notification;

use Cake\Mailer\Email;
use Notifications\Notification\Notification;

class EmailNotification extends Notification implements NotificationInterface
{

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
     * Reset all the internal variables to be able to send out a new email.
     *
     * @return $this
     */
    public function reset()
    {
        parent::reset();
        $this->_email = [];

        return $this;
    }

}
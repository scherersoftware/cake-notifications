<?php
namespace Notifications\Notification;

use Notifications\Notification\Notification;

class SmsNotification extends Notification implements NotificationInterface
{

    /**
     * Maximum message length - RFC 2822 - 140 octets
     *
     * @var int
     */
    const MAX_LENGTH = 140;

    /**
     * Recipient of the sms
     *
     * @var array
     */
    protected $_to = [];

    /**
     * Message of the sms
     *
     * @var array
     */
    protected $_message = [];

    /**
     * Constructor
     * 
     */
    public function __construct()
    {
    }

    /**
     * To
     *
     * @param string|array|null $number Null to get, String with number,
     *   Array with numbers as values
     * @return array|$this
     */
    public function to($number = null)
    {
    }

    /**
     * Message
     *
     * @param string|null $message Message of the SMS - max 140 characters
     * @return array|$this
     */
    public function message($message = null)
    {
    }

    /**
     * Message
     *
     * @return bool
     */
    public function send()
    {
    }

    /**
     * Reset all the internal variables to be able to send out a new sms.
     *
     * @return $this
     */
    public function reset()
    {
        parent::reset();
        $this->_to = [];
        $this->_message = [];

        return $this;
    }

}
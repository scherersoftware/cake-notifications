<?php
namespace Notifications\Notification;

use InvalidArgumentException;
use Notifications\Notification\Notification;
use Notifications\Transport\Transport;
use Josegonzalez\CakeQueuesadilla\Queue\Queue;

class SmsNotification extends Notification implements NotificationInterface
{

    /**
     * Holds the regex pattern for the phone number validation
     *
     * @var string
     */
    const PHONE_NUMBER_PATTERN = '/(^\+)|^[0-9]/';

    /**
     * Transport class
     *
     * @var string
     */
    protected $_transport = '\Notifications\Transport\SmsTransport';

    /**
     * Recipient of the sms
     *
     * @var string
     */
    protected $_to = '';

    /**
     * Message of the sms
     *
     * @var string
     */
    protected $_message = '';

    /**
     * To
     *
     * @param string|null $number Null to get, String with number
     * @return string|$this
     */
    public function to($number = null)
    {
        if ($number === null) {
            return $this->_to;
        }
        return $this->__setTo($number);
    }

    /**
     * Message
     *
     * @param string|null $message Message of the SMS
     * @return string|$this
     */
    public function message($message = null)
    {
        if ($message === null) {
            return $this->_message;
        }
        $this->_message = $message;
        return $this;
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
            'sms' => $this
        ], $this->_settings);
    }

    /**
     * Message
     *
     * @return bool
     */
    public function send()
    {
        Transport::factory('sms')->sendNotification($this);
    }

    /**
     * Set To
     *
     * @return $this
     */
    private function __setTo($number)
    {
        if (preg_match(self::PHONE_NUMBER_PATTERN, $number) == false) {
            throw new InvalidArgumentException(sprintf('Invalid phone number: "%s"', $number));
        }
        $this->_to = $number;
        return $this;
    }

}
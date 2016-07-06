<?php
namespace Notifications\Test\TestCase\Notification;

use Cake\Mailer\Email;
use Cake\TestSuite\TestCase;
use Notifications\Transport\EmailTransport;

/**
 * Helper class to test callbacks
 *
 */
class Foo {
    public function bar($arg1 = null, $arg2 = null)
    {
        return $arg1 . ' ' . $arg2;
    }

    public static function barStatic($arg1 = null, $arg2 = null)
    {
        return $arg1 . ' ' . $arg2;
    }
}

class EmailTransportTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        Email::dropTransport('debug');
        Email::configTransport('debug', [
            'className' => 'Debug'
        ]);

        $this->Transport = new EmailTransport();
    }

    public function testSendNotification() {
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    public function testProcessQueueObject() {
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
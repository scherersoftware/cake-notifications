<?php
namespace Notifications\Test\TestCase\Notification;

use Cake\Mailer\Email;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Notifications\Notification\EmailNotification;
use Notifications\Transport\EmailTransport;

/**
 * Helper class to test callbacks
 *
 */
class Foo {

    public function bar()
    {
    }

    public static function barStatic()
    {
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
    }

    public function testSendNotification() {
        $email = new EmailNotification();
        $email->to('foo@bar.com')
            ->beforeSendCallback(['Notifications\Test\TestCase\Notification\Foo', 'bar'])
            ->afterSendCallback('Notifications\Test\TestCase\Notification\Foo::barStatic')
            ->transport('debug');
        EmailTransport::sendNotification($email);
    }

    public function testProcessQueueObject() {
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
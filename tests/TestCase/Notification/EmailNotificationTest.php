<?php
namespace Notifications\Test\TestCase\Notification;

use Cake\Mailer\Email;
use Cake\TestSuite\TestCase;
use Notifications\Notification\NotificationFactory;

class EmailNotificationTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        
        Email::dropTransport('debug');
        Email::configTransport('debug', [
            'className' => 'Debug'
        ]);

        $this->Notification = NotificationFactory::create('email', [
            'transport' => 'debug',
            'from' => 'foo@bar.com'
        ]);
    }

    /**
     * testBeforeSendCallback method
     *
     * @return void
     */
    public function testBeforeSendCallback()
    {
        $this->Notification->beforeSendCallback('\Foo::bar', [
            'fistParameter' => 'foo',
            'secondParameter' => 'bar'
        ]);
        $this->assertEquals([
            'class' => 'Foo::bar',
            'args' => [
                'fistParameter' => 'foo',
                'secondParameter' => 'bar'
            ]
            
        ],
        [
            $this->Notification->beforeSendCallback()
        ]);

        $this->Notification->beforeSendCallback(['Foo', 'bar'], [
            'fistParameter' => 'foo',
            'secondParameter' => 'bar'
        ]);
        $this->assertEquals([
            'class' => [
                'Foo',
                'bar'
            ],
            'args' => [
                'fistParameter' => 'foo',
                'secondParameter' => 'bar'
            ]
            
        ],
        [
            $this->Notification->beforeSendCallback()
        ]);
    }

    /**
     * testAfterSendCallback method
     *
     * @return void
     */
    public function testAfterSendCallback()
    {
        $this->Notification->afterSendCallback('\Foo::bar', [
            'fistParameter' => 'foo',
            'secondParameter' => 'bar'
        ]);
        $this->assertEquals([
            'class' => 'Foo::bar',
            'args' => [
                'fistParameter' => 'foo',
                'secondParameter' => 'bar'
            ]
            
        ],
        [
            $this->Notification->afterSendCallback()
        ]);

        $this->Notification->afterSendCallback(['Foo', 'bar'], [
            'fistParameter' => 'foo',
            'secondParameter' => 'bar'
        ]);
        $this->assertEquals([
            'class' => [
                'Foo',
                'bar'
            ],
            'args' => [
                'fistParameter' => 'foo',
                'secondParameter' => 'bar'
            ]
            
        ],
        [
            $this->Notification->afterSendCallback()
        ]);
    }

    /**
     * testQueue method
     *
     * @return void
     */
    public function testQueue()
    {
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * testPush method
     *
     * @return void
     */
    public function testPush()
    {
        $this->assertTrue($this->Notification->push());
    }
}
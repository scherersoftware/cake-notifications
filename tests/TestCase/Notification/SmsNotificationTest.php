<?php
namespace Notifications\Test\TestCase\Notification;

use Cake\Mailer\Email;
use Cake\TestSuite\TestCase;
use Notifications\Notification\Notification;

class SmsNotificationTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->Notification = Notification::factory('sms');
    }

    /**
     * testBeforeSendCallback method
     *
     * @return void
     */
    public function testBeforeSendCallback()
    {
        $this->Notification->beforeSendCallback('Foo::bar', [
            'fistParameter' => 'foo',
            'secondParameter' => 'bar'
        ]);

        $this->assertEquals([
            'class' => 'Foo::bar',
            'args' => [
                'fistParameter' => 'foo',
                'secondParameter' => 'bar'
            ]
        ], $this->Notification->beforeSendCallback());

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
        ], $this->Notification->beforeSendCallback());
    }

    /**
     * testAfterSendCallback method
     *
     * @return void
     */
    public function testAfterSendCallback()
    {
        $this->Notification->afterSendCallback('Foo::bar', [
            'fistParameter' => 'foo',
            'secondParameter' => 'bar'
        ]);
        $this->assertEquals([
            'class' => 'Foo::bar',
            'args' => [
                'fistParameter' => 'foo',
                'secondParameter' => 'bar'
            ]
        ], $this->Notification->afterSendCallback());

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
        ], $this->Notification->afterSendCallback());
    }

    /**
     * testQueue method
     *
     * @return void
     */
    public function testTo()
    {
        $this->Notification->to('12345678');
        $this->assertEquals('12345678' , $this->Notification->to());

        $this->Notification->to('+12345678');
        $this->assertEquals('+12345678' , $this->Notification->to());

        $this->setExpectedException('InvalidArgumentException');
        $this->Notification->to('foo_bar');;
    }

    /**
     * testQueue method
     *
     * @return void
     */
    public function testMessage()
    {
        $message = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et ma';
        $this->Notification->message($message);
        $this->assertEquals($message, $this->Notification->message());
    }

    /**
     * testQueue method
     *
     * @return void
     */
    public function testSettings()
    {
        $settings = [
            'attempts' => 20,
            'attempts_delay' => 2,
            'delay' => 2,
            'expires_in' => 10,
            'queue' => 'email'
        ];
        $this->Notification->settings($settings);
        $this->assertEquals($settings, $this->Notification->settings());
    }

    /**
     * testSend method
     *
     * @return void
     */
    public function testSend()
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
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
<?php
namespace Notifications\Test\TestCase\Notification;

use Cake\Mailer\Email;
use Cake\TestSuite\TestCase;
use Notifications\Notification\Notification;

class EmailNotificationTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        
        Email::dropTransport('debug');
        Email::configTransport('debug', [
            'className' => 'Debug'
        ]);

        $this->Notification = Notification::factory('email', [
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
        $this->Notification->beforeSendCallback('Foo::bar', [
            'foo',
            'bar'
        ]);

        $this->assertEquals([
            'class' => 'Foo::bar',
            'args' => [
                'foo',
                'bar'
            ]
        ], $this->Notification->beforeSendCallback());

        $this->Notification->beforeSendCallback(['Foo', 'bar'], [
            'foo',
            'bar'
        ]);
        $this->assertEquals([
            'class' => [
                'Foo',
                'bar'
            ],
            'args' => [
                'foo',
                'bar'
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
            'foo',
            'bar'
        ]);
        $this->assertEquals([
            'class' => 'Foo::bar',
            'args' => [
                'foo',
                'bar'
            ]
        ], $this->Notification->afterSendCallback());

        $this->Notification->afterSendCallback(['Foo', 'bar'], [
            'foo',
            'bar'
        ]);
        $this->assertEquals([
            'class' => [
                'Foo',
                'bar'
            ],
            'args' => [
                'foo',
                'bar'
            ]
        ], $this->Notification->afterSendCallback());
    }

    /**
     * testSettings method
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
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
    public function testTo()
    {
        $this->Notification->to('01605144878');
        $this->assertEquals('+491605144878' , $this->Notification->to());

        $this->Notification->to('0160/5144878');
        $this->assertEquals('+491605144878' , $this->Notification->to());

        $this->Notification->to('0160 5144878');
        $this->assertEquals('+491605144878' , $this->Notification->to());

        $this->Notification->to('+4901605144878');
        $this->assertEquals('+4901605144878' , $this->Notification->to());

        $this->Notification->to('4901605144878');
        $this->assertEquals('+4901605144878' , $this->Notification->to());

        $this->Notification->to('');
        $this->setExpectedException('InvalidArgumentException');

        $this->Notification->to('123456789');
        $this->setExpectedException('InvalidArgumentException');

        $this->Notification->to('+491605144878123456');
        $this->setExpectedException('InvalidArgumentException');

        $this->Notification->to([
            '01605144878',
            '01605144879'
        ]);
        $this->assertEquals([
            '+491605144878',
            '+491605144879'
        ], $this->Notification->to());
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

        $message1 = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec qu';
        $this->Notification->message($message1);
        $this->setExpectedException('InvalidArgumentException');

        $message2 = '';
        $this->Notification->message($message2);
        $this->setExpectedException('InvalidArgumentException');
    }

    /**
     * testQueue method
     *
     * @return void
     */
    public function testQueue()
    {
        $this->Notification->queue('default');
        $this->assertEquals('default', $this->Notification->queue());
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

    /**
     * testSend method
     *
     * @return void
     */
    public function testSend()
    {
        $this->assertTrue($this->Notification->send());
    }

    /**
     * testReset method
     *
     * @return void
     */
    public function testReset()
    {
        $this->Notification->reset();
        $sms = Notification::factory('sms');
        $this->assertEquals($this->Notification, $sms);
    }
}
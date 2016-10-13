<?php
namespace Notifications\Test\TestCase\Notification;

use Cake\I18n\I18n;
use Cake\TestSuite\TestCase;
use Notifications\Notification\EmailNotification;
use Notifications\Transport\EmailTransport;

/**
 * Helper class to test callbacks
 *
 */
class SomeClass
{

    public static $someProperty = null;
    public static $anotherProperty = null;

    public function someMethod()
    {
        self::$someProperty = 'was_called';
    }

    public static function someStaticMethod()
    {
        self::$anotherProperty = 'was_called';
    }
}

class EmailTransportTest extends TestCase
{

    public function testSendNotification()
    {
        $email = new EmailNotification([
            'transport' => 'debug',
            'from' => 'foo@bar.com'
        ]);
        $email->to('foo@bar.com')
            ->locale('de_DE')
            ->beforeSendCallback(['Notifications\Test\TestCase\Notification\SomeClass', 'someMethod'])
            ->afterSendCallback('Notifications\Test\TestCase\Notification\SomeClass::someStaticMethod');
        EmailTransport::sendNotification($email);

        $this->assertEquals(I18n::locale(), 'de_DE');

        $this->assertEquals(SomeClass::$someProperty, 'was_called');
        $this->assertEquals(SomeClass::$anotherProperty, 'was_called');

    }

    public function testProcessQueueObject()
    {
        
    }
}

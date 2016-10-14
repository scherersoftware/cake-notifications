<?php
namespace Notifications\Test\TestCase\Notification;

use Cake\I18n\I18n;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Josegonzalez\CakeQueuesadilla\Queue\Queue;
use Notifications\Notification\EmailNotification;
use Notifications\Transport\EmailTransport;

/**
 * Helper class to test callbacks
 *
 */
class SomeClass
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'Jobs' => 'plugin.notifications.jobs'
    ];

    public static $someProperty = null;
    public static $anotherProperty = null;
    public static $wasTheCallableCalled = false;

    public function someMethod()
    {
        self::$someProperty = 'was_called';
        return function (EmailNotification $email) {
            self::$wasTheCallableCalled = true;
        };
    }

    public static function someStaticMethod()
    {
        self::$anotherProperty = 'was_called';
    }
}

class EmailTransportTest extends TestCase
{

    public function setup()
    {
        parent::setUp();
        Log::reset();
        Log::config('stdout', ['engine' => 'File']);

        $dbConfig = \Cake\Datasource\ConnectionManager::config('test');

        Queue::reset();
        Queue::config([
            'default' => [
                'engine' => 'josegonzalez\Queuesadilla\Engine\MysqlEngine',
                'database' => $dbConfig['database'],
                'host' => $dbConfig['host'],
                'user' => $dbConfig['username'],
                'pass' => $dbConfig['password']
            ]
        ]);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        Log::reset();
        Queue::reset();
    }

    public function testNotExistingCallbackCall()
    {
        $email = new EmailNotification([
            'transport' => 'debug',
            'from' => 'foo@bar.com'
        ]);
        $email->to('foo@bar.com')
            ->beforeSendCallback(['SomeClassThatDoesNotExists', 'someMethod']);
        $return = EmailTransport::sendNotification($email);

        $this->assertEquals(SomeClass::$someProperty, null);
        $this->assertEquals(SomeClass::$anotherProperty, null);
        $this->assertFalse(SomeClass::$wasTheCallableCalled);
    }

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
        $this->assertTrue(SomeClass::$wasTheCallableCalled);
    }

    public function testProcessQueueObject()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}

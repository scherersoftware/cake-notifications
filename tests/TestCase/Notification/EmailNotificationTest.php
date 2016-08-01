<?php
namespace Notifications\Test\TestCase\Notification;

use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\Mailer\Email;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Josegonzalez\CakeQueuesadilla\Queue\Queue;
use Notifications\Notification\EmailNotification;

class EmailNotificationTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'Jobs' => 'plugin.notifications.jobs'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
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

        Email::dropTransport('debug');
        Email::configTransport('debug', [
            'className' => 'Debug'
        ]);

        $this->Notification = new EmailNotification([
            'transport' => 'debug',
            'from' => 'foo@bar.com'
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

        unset($this->Notification);
        Log::reset();
        Queue::reset();
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
     * testEmail method
     *
     * @return void
     */
    public function testEmail()
    {
        $emailNotification = new EmailNotification();
        $this->assertEquals(new Email(), $emailNotification->email());
    }

    /**
     * testSettings method
     *
     * @return void
     */
    public function testQueueOptions()
    {
        $options = [
            'attempts' => 20,
            'attempts_delay' => 2,
            'delay' => 2,
            'expires_in' => 10,
            'queue' => 'email'
        ];
        $this->Notification->queueOptions($options);
        $this->assertEquals($options, $this->Notification->queueOptions());
    }

    /**
     * testLocale method
     *
     * @return void
     */
    public function testLocale()
    {
        $this->Notification->locale('de_DE');
        $this->assertEquals('de_DE', $this->Notification->locale());
    }

    /**
     * testPush method
     *
     * @return void
     */
    public function testPush()
    {
        $this->Notification->push();

        $job = TableRegistry::get('Jobs')->find()
            ->first();
        $this->assertTrue($job->id === 1);
        $this->assertTrue($job->queue === 'default');
    }

    /**
     * testSend method
     *
     * @return void
     */
    public function testSend()
    {
        $this->Notification->to('foo@bar.de')
            ->send();
    }
}

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
     * testMissingLocale method
     *
     * @return void
     */
    public function testMissingLocale()
    {
        $this->expectException('\InvalidArgumentException');

        Configure::delete('Notifications.defaultLocale');
        $this->Notification = new EmailNotification();
    }

    /**
     * testBeforeSendCallback method
     *
     * @return void
     */
    public function testBeforeSendCallback()
    {
        $this->Notification->beforeSendCallback('Foo::bar', ['foo', 'bar']);
        $this->assertEquals([
            [
                'class' => 'Foo::bar',
                'args' => [
                    'foo',
                    'bar'
                ]
            ]
        ], $this->Notification->beforeSendCallback());

        $this->Notification->beforeSendCallback(['Foo', 'bar'], ['foo', 'bar']);
        $this->assertEquals([
            [
                'class' => [
                    'Foo',
                    'bar'
                ],
                'args' => [
                    'foo',
                    'bar'
                ]
            ]
        ], $this->Notification->beforeSendCallback());
    }

    /**
     * testAddBeforeSendCallback method
     *
     * @return void
     */
    public function testAddBeforeSendCallback()
    {
        $this->Notification->beforeSendCallback('Foo::bar', ['foo', 'bar']);
        $this->Notification->addBeforeSendCallback('Foo::bar', ['foo1', 'bar1']);
        $this->Notification->addBeforeSendCallback(['Foo', 'bar'], ['foo2', 'bar2']);

        $this->assertEquals([
            [
                'class' => 'Foo::bar',
                'args' => [
                    'foo',
                    'bar'
                ]
            ],
            [
                'class' => 'Foo::bar',
                'args' => [
                    'foo1',
                    'bar1'
                ]
            ],
            [
                'class' => [
                    'Foo',
                    'bar'
                ],
                'args' => [
                    'foo2',
                    'bar2'
                ]
            ],
        ], $this->Notification->beforeSendCallback());
    }

    /**
     * testAfterSendCallback method
     *
     * @return void
     */
    public function testAfterSendCallback()
    {
        $this->Notification->afterSendCallback('Foo::bar', ['foo', 'bar']);
        $this->assertEquals([
            [
                'class' => 'Foo::bar',
                'args' => [
                    'foo',
                    'bar'
                ]
            ]
        ], $this->Notification->afterSendCallback());

        $this->Notification->afterSendCallback(['Foo', 'bar'], [
            'foo',
            'bar'
        ]);
        $this->assertEquals([
            [
                'class' => [
                    'Foo',
                    'bar'
                ],
                'args' => [
                    'foo',
                    'bar'
                ]
            ]
        ], $this->Notification->afterSendCallback());
    }

    /**
     * testAddAfterSendCallback method
     *
     * @return void
     */
    public function testAddAfterSendCallback()
    {
        $this->Notification->addAfterSendCallback('Foo::bar', ['foo', 'bar']);
        $this->Notification->addAfterSendCallback('Foo::bar', ['foo1', 'bar1']);
        $this->Notification->addAfterSendCallback(['Foo', 'bar'], ['foo2', 'bar2']);

        $this->assertEquals([
            [
                'class' => 'Foo::bar',
                'args' => [
                    'foo',
                    'bar'
                ]
            ],
            [
                'class' => 'Foo::bar',
                'args' => [
                    'foo1',
                    'bar1'
                ]
            ],
            [
                'class' => [
                    'Foo',
                    'bar'
                ],
                'args' => [
                    'foo2',
                    'bar2'
                ]
            ],
        ], $this->Notification->afterSendCallback());
    }

    /**
     * testMissformatedCallback method
     *
     * @return void
     */
    public function testMissformatedCallback()
    {
        $this->expectException('\InvalidArgumentException');
        $this->Notification->beforeSendCallback(['Foo'], ['foo', 'bar']);
    }

    /**
     * testMissformatedCallback method
     *
     * @return void
     */
    public function testMissformatedAddCallback()
    {
        $this->expectException('\InvalidArgumentException');
        $this->Notification->addBeforeSendCallback(['Foo'], ['foo', 'bar']);
    }

    /**
     * testEmail method
     *
     * @return void
     */
    public function testEmail()
    {
        // $emailNotification = new EmailNotification();
        // $this->assertEquals(new Email(), $emailNotification->email());
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

        $jobs = TableRegistry::get('Jobs')->find()
            ->count();
        $this->assertTrue($jobs === 2);
    }

    /**
     * testSend method
     *
     * @return void
     */
    public function testSend()
    {
        $email = $this->Notification->to('foo@bar.de')
            ->send();
        $this->assertNotEmpty($email);
    }
}

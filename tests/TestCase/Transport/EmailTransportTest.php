<?php
namespace Notifications\Test\TestCase\Notification;

use Cake\Datasource\ConnectionManager;
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
        $connection = ConnectionManager::get('test');
        $connection->execute('INSERT INTO foos (data) VALUES ("bar was called")');
    }

    public static function barStatic()
    {
        $connection = ConnectionManager::get('test');
        $connection->execute('INSERT INTO foos (data) VALUES ("barStatic was called")');
    }
}

class EmailTransportTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'Jobs' => 'plugin.notifications.foos'
    ];

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
        $connection = ConnectionManager::get('test');
        $result = $connection->execute('SELECT data FROM foos WHERE id = 1')->fetch('assoc');
        $this->assertEquals('bar was called', $result['data']);
        $result = $connection->execute('SELECT data FROM foos WHERE id = 2')->fetch('assoc');
        $this->assertEquals('barStatic was called', $result['data']);
    }

    public function testProcessQueueObject() {
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}

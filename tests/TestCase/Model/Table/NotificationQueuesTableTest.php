<?php
namespace Notifications\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Notifications\Model\Table\NotificationQueuesTable;
use Notifications\Transport\Transport;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;
use Cake\I18n\Time;

/**
 * Notifications\Model\Table\NotificationQueuesTable Test Case
 */
class NotificationQueuesTableTest extends TestCase {

/**
 * Fixtures
 *
 * @var array
 */
    public $fixtures = [
        'plugin.notifications.i18n',
        'plugin.notifications.notification_queue',
        'plugin.notifications.notification_content',
        'plugin.notifications.user'
    ];

/**
 * setUp method
 *
 * @return void
 */
    public function setUp() {
        parent::setUp();
        TableRegistry::clear();

        $config = TableRegistry::exists('Notifications.NotificationQueue') ? [] : ['className' => 'Notifications\Model\Table\NotificationQueueTable'];
        $this->NotificationQueue = TableRegistry::get('Notifications.NotificationQueues', $config);
        $config = TableRegistry::exists('Notifications.NotificationContents') ? [] : ['className' => 'Notifications\Model\Table\NotificationContentsTable'];
        $this->NotificationContents = TableRegistry::get('Notifications.NotificationContents', $config);
    }

/**
 * tearDown method
 *
 * @return void
 */
    public function tearDown() {
        unset($this->NotificationQueue);
        unset($this->NotificationContents);
        TableRegistry::clear();
        parent::tearDown();
    }

    public function testAddBasicEmailNotification() {
        $identifier = 'test_email_notification_custom';
        $this->_createNotificationContent($identifier);

        $data = [
            'locale' => 'eng',
            'recipient_user_id' => 'f9df9eab-a6a3-4c89-9579-3eaeeb47e25f',
            'transport' => 'email',
            'config' => [
                'placeholder1' => 'PL1',
                'placeholder2' => 'PL2'
            ]
        ];
        $notification = $this->NotificationQueue->createNotification($identifier, $data);

        $this->assertEquals(get_class($notification), 'Notifications\Model\Entity\Notification');
        $res = $this->NotificationQueue->enqueue($notification);

        $this->assertEquals($res, $notification);
        $queuedNotification = $this->NotificationQueue->find()->first();
        $this->assertEquals($queuedNotification->locale, $data['locale']);
        $this->assertEquals($queuedNotification->recipient_user_id, $data['recipient_user_id']);
        $this->assertEquals($queuedNotification->transport, $data['transport']);
        $this->assertEquals($queuedNotification->config, $data['config']);

        $this->assertFalse($queuedNotification->locked);
        $this->assertFalse($queuedNotification->sent);
        $this->assertEquals($queuedNotification->send_tries, 0);
        $this->assertEquals($queuedNotification->notification_identifier, $identifier);
    }

    public function testNotificationLocking() {
        $identifier = 'test_email_notification_custom';
        $this->_createNotificationContent($identifier);

        $data = [
            'locale' => 'eng',
            'recipient_user_id' => 'f9df9eab-a6a3-4c89-9579-3eaeeb47e25f',
            'transport' => 'email',
            'config' => [
                'placeholder1' => 'PL1',
                'placeholder2' => 'PL2'
            ]
        ];
        $notification = $this->NotificationQueue->createNotification($identifier, $data);
        $res = $this->NotificationQueue->enqueue($notification);

        $queuedNotification = $this->NotificationQueue->find()->first();

        $this->assertFalse($queuedNotification->locked);

        $lockResult = $this->NotificationQueue->lock([ $notification->id ]);
        $this->assertTrue($lockResult);

        $queuedNotification = $this->NotificationQueue->find()->first();
        $this->assertTrue($queuedNotification->locked);

        $this->NotificationQueue->releaseLocks([ $notification->id ]);
        $queuedNotification = $this->NotificationQueue->find()->first();
        $this->assertFalse($queuedNotification->locked);

        $this->NotificationQueue->lock([ $notification->id ]);

        $this->NotificationQueue->clearLocks();

        $queuedNotification = $this->NotificationQueue->find()->first();
        $this->assertFalse($queuedNotification->locked);
    }

    public function testNotificationFailing() {
        $identifier = 'test_email_notification_custom';
        $this->_createNotificationContent($identifier);

        $data = [
            'locale' => 'eng',
            'recipient_user_id' => 'f9df9eab-a6a3-4c89-9579-3eaeeb47e25f',
            'transport' => 'email',
            'config' => [
                'placeholder1' => 'PL1',
                'placeholder2' => 'PL2'
            ]
        ];
        $notification = $this->NotificationQueue->createNotification($identifier, $data);
        $res = $this->NotificationQueue->enqueue($notification);

        $queuedNotification = $this->NotificationQueue->find()->first();
        $this->assertEquals($queuedNotification->send_tries, 0);

        $this->NotificationQueue->fail($queuedNotification->id);
        $queuedNotification = $this->NotificationQueue->find()->first();
        $this->assertEquals($queuedNotification->send_tries, 1);

        $this->NotificationQueue->fail($queuedNotification->id);
        $queuedNotification = $this->NotificationQueue->find()->first();
        $this->assertEquals($queuedNotification->send_tries, 2);
    }

    public function testNotificationSucceeding() {
        $identifier = 'test_email_notification_custom';
        $this->_createNotificationContent($identifier);

        $data = [
            'locale' => 'eng',
            'recipient_user_id' => 'f9df9eab-a6a3-4c89-9579-3eaeeb47e25f',
            'transport' => 'email',
            'config' => [
                'placeholder1' => 'PL1',
                'placeholder2' => 'PL2'
            ]
        ];
        $notification = $this->NotificationQueue->createNotification($identifier, $data);
        $res = $this->NotificationQueue->enqueue($notification);

        $queuedNotification = $this->NotificationQueue->find()->first();
        $this->assertEquals($queuedNotification->sent, false);

        $this->NotificationQueue->success($queuedNotification->id);
        $queuedNotification = $this->NotificationQueue->find()->first();
        $this->assertEquals($queuedNotification->sent, true);
    }

    public function testGetBatch() {
        $identifier = 'test_email_notification_custom';
        $this->_createNotificationContent($identifier);

        $data = [
            'locale' => 'eng',
            'recipient_user_id' => 'f9df9eab-a6a3-4c89-9579-3eaeeb47e25f',
            'transport' => 'email',
            'config' => [
                'placeholder1' => 'PL1',
                'placeholder2' => 'PL2'
            ]
        ];
        $notification1 = $this->NotificationQueue->createNotification($identifier, $data);
        $res1 = $this->NotificationQueue->enqueue($notification1);

        $notification2 = $this->NotificationQueue->createNotification($identifier, $data);
        $notification2->created = Time::createFromTimestamp(strtotime('+5hour')); // this is critical, as we're ordering by created when getting a batch

        $res2 = $this->NotificationQueue->enqueue($notification2);

        $batch = $this->NotificationQueue->getBatch(1);
        $this->assertTrue(is_array($batch));
        $this->assertEquals(count($batch), 1);

        $firstNotification = $this->NotificationQueue->get($notification1->id);
        $secondNotification = $this->NotificationQueue->get($notification2->id);

        $this->assertTrue($firstNotification->locked);
        $this->assertFalse($secondNotification->locked);
        $this->assertEquals($batch[0]->id, $firstNotification->id);

        // notification1 must not be in the batch, as it is locked by the first batch
        $batch2 = $this->NotificationQueue->getBatch();
        $this->assertTrue(is_array($batch2));
        $this->assertEquals(count($batch2), 1);
        $this->assertEquals($batch2[0]->id, $secondNotification->id);

        $this->NotificationQueue->clearLocks();

        // make sure sent notifications are not added to the batch
        $this->NotificationQueue->success($notification1->id);
        $batch2 = $this->NotificationQueue->getBatch();
        $this->assertTrue(is_array($batch2));
        $this->assertEquals(count($batch2), 1);
        $this->assertEquals($batch2[0]->id, $secondNotification->id);

        $this->NotificationQueue->clearLocks();
        $notification1->sent = false;
        $this->NotificationQueue->save($notification1);

        // make sure notifications which have reached the max send tries are not in the batch
        $notification2->send_tries = $this->NotificationQueue->getMaxSendTries();
        $this->NotificationQueue->save($notification2);
        $batch3 = $this->NotificationQueue->getBatch();
        $this->assertTrue(is_array($batch3));
        $this->assertEquals(count($batch3), 1);

        $this->assertEquals($batch3[0]->id, $firstNotification->id);

        // notification to be sent in the future shouldn't be in the batch
        $data['send_after'] = Time::parse('+2 hours');
        $notification3 = $this->NotificationQueue->createNotification($identifier, $data);
        $this->NotificationQueue->save($notification3);
        $batch4 = $this->NotificationQueue->getBatch();
        $this->assertTrue(is_array($batch4));
        $this->assertEmpty(count($batch4));
    }

    public function testRenderNotification() {
        $identifier = 'test_email_notification_custom';
        $content = $this->_createNotificationContent($identifier);

        $data = [
            'locale' => 'eng',
            'recipient_user_id' => 'f9df9eab-a6a3-4c89-9579-3eaeeb47e25f',
            'transport' => 'email',
            'config' => [
                'placeholder1' => 'PL1',
                'placeholder2' => 'PL2'
            ]
        ];
        $notification = $this->NotificationQueue->createNotification($identifier, $data, true);

        $renderedSubject = $content->render('email_subject', $notification);
        $this->assertEquals($renderedSubject, 'Test Subject with PL1');

        $renderedBody = $content->render('email_text', $notification);
        $this->assertEquals($renderedBody, 'body with PL1 and PL2');
    }

    public function testSendWithEmailTransport() {
        $identifier = 'test_email_notification_custom';
        $content = $this->_createNotificationContent($identifier);

        $data = [
            'locale' => 'eng',
            'recipient_user_id' => 'f9df9eab-a6a3-4c89-9579-3eaeeb47e25f',
            'transport' => 'email',
            'config' => [
                'placeholder1' => 'PL1',
                'placeholder2' => 'PL2'
            ]
        ];
        $notification = $this->NotificationQueue->createNotification($identifier, $data, true);

        $user = $this->NotificationQueue->RecipientUsers->get($notification->recipient_user_id);

        $emailTransport = new \Cake\Network\Email\DebugTransport();
        $res = $this->NotificationQueue->send($notification, [
            'emailTransport' => $emailTransport,
            'templated' => false
        ]);

        $this->assertTrue(is_array($res));
        $this->assertContains('<' . $user->email . '>', $res['headers']);
        $this->assertContains('Subject: Test Subject with PL1', $res['headers']);
        $this->assertEquals(trim($res['message']), 'body with PL1 and PL2');
    }

    public function testSendWithEmailTransportAndAttachment() {
        $identifier = 'test_email_notification_custom';
        $content = $this->_createNotificationContent($identifier);


        $testAttachmentPath = ROOT . '/tmp/tests/test_attachment.txt';
        file_put_contents($testAttachmentPath, 'foobar');
        $testAttachmentName = 'my_attachment.txt';

        $data = [
            'locale' => 'eng',
            'recipient_user_id' => 'f9df9eab-a6a3-4c89-9579-3eaeeb47e25f',
            'transport' => 'email',
            'config' => [
                'placeholder1' => 'PL1',
                'placeholder2' => 'PL2',
                'attachments' => [
                    $testAttachmentName => $testAttachmentPath
                ]
            ]
        ];
        $notification = $this->NotificationQueue->createNotification($identifier, $data, true);

        $user = $this->NotificationQueue->RecipientUsers->get($notification->recipient_user_id);

        $emailTransport = new \Cake\Network\Email\DebugTransport();
        $res = $this->NotificationQueue->send($notification, [
            'emailTransport' => $emailTransport
        ]);

        $this->assertTrue(is_array($res));
        $this->assertContains('<' . $user->email . '>', $res['headers']);
        $this->assertContains('Subject: Test Subject with PL1', $res['headers']);

        $this->assertContains('Content-Type: multipart/mixed', $res['headers']);
        $this->assertContains('Content-Disposition: attachment; filename="my_attachment.txt"', $res['message']);

        unlink($testAttachmentPath);
    }

    public function testCreateNotificationsWithMultipleTransports() {
        $identifier = 'test_email_notification_custom';
        $content = $this->_createNotificationContent($identifier, [
            'push_message' => 'push message content with {{placeholder1}}'
        ]);

        $data = [
            'locale' => 'eng',
            'recipient_user_id' => 'f9df9eab-a6a3-4c89-9579-3eaeeb47e25f',
            'transport' => ['email', 'push_message'],
            'config' => [
                'placeholder1' => 'PL1',
                'placeholder2' => 'PL2'
            ]
        ];
        $notification = $this->NotificationQueue->createNotifications($identifier, $data, true);

        $queuedNotifications = $this->NotificationQueue->find('all')->toArray();
        $this->assertTrue(is_array($queuedNotifications));
        $this->assertEquals(count($queuedNotifications), 2);

        $notificationsWithEmailTransportCount = $this->NotificationQueue->find()->where([
            'transport' => 'email'
        ])->count();
        $notificationsWithPushMessageTransportCount = $this->NotificationQueue->find()->where([
            'transport' => 'push_message'
        ])->count();
        $this->assertEquals($notificationsWithEmailTransportCount, 1);
        $this->assertEquals($notificationsWithPushMessageTransportCount, 1);
    }

    public function testTransportFactory() {
        $emailTransport = Transport::factory('email');
        $this->assertTrue($emailTransport instanceof \Notifications\Transport\EmailTransport);

        $pushMessageTransport = Transport::factory('push_message');
        $this->assertTrue($pushMessageTransport instanceof \Notifications\Transport\PushMessageTransport);

        $smsTransport = Transport::factory('sms');
        $this->assertTrue($smsTransport instanceof \Notifications\Transport\SmsTransport);

        try {
            $invalidTransport = Transport::factory('invalid');
            $this->fail('Transport::factory should throw an InvalidArgumentException if an invalid type is given');
        } catch(\InvalidArgumentException $e) {
        }

    }

    public function testCreateNotificationWithInvalidIdentifier() {
        $data = [
            'locale' => 'eng',
            'recipient_user_id' => 'f9df9eab-a6a3-4c89-9579-3eaeeb47e25f',
            'transport' => 'email',
            'config' => [
                'placeholder1' => 'PL1',
                'placeholder2' => 'PL2'
            ]
        ];
        try {
            $notification = $this->NotificationQueue->createNotification('INVALID', $data, true);
            $this->fail('Passing an invalid notification content identifier should result in an exception.');
        } catch(\InvalidArgumentException $e) {
        }
    }


    protected function _createNotificationContent($identifier, array $data = []) {
        $this->NotificationContents->locale('eng');
        $entity = $this->NotificationContents->newEntity(Hash::merge([
            'notification_identifier' => $identifier,
            'email_subject' => 'Test Subject with {{placeholder1}}',
            'email_text' => 'body with {{placeholder1}} and {{placeholder2}}',
            'email_html' => 'body with {{placeholder1}} and {{placeholder2}}',
        ], $data));
        return $this->NotificationContents->save($entity);
    }
}

<?php
namespace Notifications\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Notifications\Model\Table\NotificationContentsTable;
use Cake\TestSuite\TestCase;
use Notifications\Model\Entity\NotificationContent;

/**
 * Notifications\Model\Table\NotificationContentsTable Test Case
 */
class NotificationContentsTableTest extends TestCase {

/**
 * Fixtures
 *
 * @var array
 */
    public $fixtures = [
        'plugin.notifications.notification_content',
        'plugin.notifications.i18n'
    ];

/**
 * setUp method
 *
 * @return void
 */
    public function setUp() {
        parent::setUp();
        $config = TableRegistry::exists('NotificationContents') ? [] : ['className' => 'Notifications\Model\Table\NotificationContentsTable'];
        $this->NotificationContents = TableRegistry::get('NotificationContents', $config);
    }

/**
 * tearDown method
 *
 * @return void
 */
    public function tearDown() {
        unset($this->NotificationContents);
        parent::tearDown();
    }

    public function testFieldValidation() {
        // make sure at least one of the transport-speficic contents is given.
        $identifier = 'test_email_notification';
        $this->NotificationContents->locale('eng');
        $entity = $this->NotificationContents->newEntity([
            'notification_identifier' => $identifier,
        ]);
        $res = $this->NotificationContents->save($entity);
        $this->assertFalse($res);
        $this->assertEquals(count($entity->errors()), 1);
    }

/**
 * @return void
 */
    public function testBasicEmailNotification() {
        $identifier = 'test_email_notification';
        $this->NotificationContents->locale('eng');
        $entity = $this->NotificationContents->newEntity([
            'notification_identifier' => $identifier,
            'email_subject' => 'Test Subject with {{placeholder1}}',
            'email_text' => 'body with {{placeholder1}} and {{placeholder2}}'
        ]);
        $this->NotificationContents->save($entity);

        $content = $this->NotificationContents->getByIdentifier($identifier, 'eng');
        $this->assertTrue($content instanceof NotificationContent);
        $this->assertEquals($content->email_subject, $entity->email_subject);
        $this->assertEquals($content->email_text, $entity->email_text);
        $this->assertEquals($content->notification_identifier, $identifier);
    }
}

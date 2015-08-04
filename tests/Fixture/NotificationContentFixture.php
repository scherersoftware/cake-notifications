<?php
namespace Notifications\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * NotificationContentFixture
 *
 */
class NotificationContentFixture extends TestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = [
		'id' => ['type' => 'uuid', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
		'notification_identifier' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'fixed' => null],
		'notes' => ['type' => 'text', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
		'created' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
		'modified' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
		'_constraints' => [
			'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
		],
		'_options' => [
'engine' => 'InnoDB', 'collation' => 'utf8_general_ci'
		],
	];

/**
 * Records
 *
 * @var array
 */
	public $records = [
        [
            'id' => 'fe700a46-240b-4d19-8dab-0ac5dd321525',
            'notification_identifier' => 'offer_sent',
            'notes' => 'Angebot wurde an den Kunden zur Freigabe geschickt. ',
            'created' =>'2014-10-17 15:45:14',
            'modified' => '2014-10-17 15:45:14'
        ],
        [
            'id' => '453804d7-6300-466b-885b-b3b314dc46ba',
            'notification_identifier' => 'offer_declined',
            'notes' => 'Angebot wurde vom Kunden abgelehnt.',
            'created' => '2014-10-17 15:45:14',
            'modified' => '2014-10-17 15:45:14'
        ],
        [
            'id' => '533955f3-0c51-4c49-93c5-145ff5042a8f',
            'notification_identifier' => 'offer_accepted',
            'notes' => 'Angebot wurde vom Kunden angenommen. ',
            'created' => '2014-10-17 15:45:14',
            'modified' => '2014-10-17 15:45:14'
        ],
        [
            'id' => 'a721c70f-619f-4bc3-a83f-33823e71bcd5',
            'notification_identifier' => 'offer_status_change',
            'notes' => 'Status des Angebotes wurde geändert.',
            'created' => '2014-10-17 15:45:14',
            'modified' => '2014-10-17 15:45:14'
        ],
        [
            'id' => 'b9d10652-39e2-11e5-817e-7abbcbc47cc1',
            'notification_identifier' => 'test_email_notification',
            'notes' => 'test',
            'created' => '2014-10-17 15:45:14',
            'modified' => '2014-10-17 15:45:14'
        ],
	];

}

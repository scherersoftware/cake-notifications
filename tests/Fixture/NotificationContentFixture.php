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
            'id' => 1,
            'notification_identifier' => 'offer_sent',
            'notes' => 'Angebot wurde an den Kunden zur Freigabe geschickt. ',
            'created' =>'2014-10-17 15:45:14',
            'modified' => '2014-10-17 15:45:14'
        ],
        [
            'id' => 2,
            'notification_identifier' => 'offer_declined',
            'notes' => 'Angebot wurde vom Kunden abgelehnt.',
            'created' => '2014-10-17 15:45:14',
            'modified' => '2014-10-17 15:45:14'
        ],
        [
            'id' => 3,
            'notification_identifier' => 'offer_accepted',
            'notes' => 'Angebot wurde vom Kunden angenommen. ',
            'created' => '2014-10-17 15:45:14',
            'modified' => '2014-10-17 15:45:14'
        ],
        [
            'id' => 4,
            'notification_identifier' => 'offer_status_change',
            'notes' => 'Status des Angebotes wurde geändert.',
            'created' => '2014-10-17 15:45:14',
            'modified' => '2014-10-17 15:45:14'
        ],
	];

}

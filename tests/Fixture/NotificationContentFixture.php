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
			'id' => '132c28a1-cada-45d7-8a86-37b6711df5f1',
			'notification_identifier' => 'supplier_invitation',
			'notes' => '- {{supplier.firstname}}\r\n- {{supplier.lastname}}\r\n- {{activateAccountUrl}}',
			'created' => '2014-10-17 15:45:14',
			'modified' => '2014-10-17 15:46:14',
		]
	];

}

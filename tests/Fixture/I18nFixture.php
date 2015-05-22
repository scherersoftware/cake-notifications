<?php
namespace Notifications\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * I18nFixture
 *
 */
class I18nFixture extends TestFixture {

/**
 * Table name
 *
 * @var string
 */
	public $table = 'i18n';

/**
 * Fields
 *
 * @var array
 */
	public $fields = [
		'id' => ['type' => 'integer', 'length' => 10, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
		'locale' => ['type' => 'string', 'length' => 6, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'fixed' => null],
		'model' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'fixed' => null],
		'foreign_key' => ['type' => 'uuid', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
		'field' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'fixed' => null],
		'content' => ['type' => 'text', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
		'_indexes' => [
			'I18N_LOCALE_ROW' => ['type' => 'index', 'columns' => ['locale', 'model', 'foreign_key'], 'length' => []],
			'I18N_LOCALE_MODEL' => ['type' => 'index', 'columns' => ['locale', 'model'], 'length' => []],
			'I18N_FIELD' => ['type' => 'index', 'columns' => ['model', 'foreign_key', 'field'], 'length' => []],
			'I18N_ROW' => ['type' => 'index', 'columns' => ['model', 'foreign_key'], 'length' => []],
			'locale' => ['type' => 'index', 'columns' => ['locale'], 'length' => []],
			'model' => ['type' => 'index', 'columns' => ['model'], 'length' => []],
			'row_id' => ['type' => 'index', 'columns' => ['foreign_key'], 'length' => []],
			'field' => ['type' => 'index', 'columns' => ['field'], 'length' => []],
		],
		'_constraints' => [
			'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
			'I18N_LOCALE_FIELD' => ['type' => 'unique', 'columns' => ['locale', 'model', 'foreign_key', 'field'], 'length' => []],
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
            'locale' => 'de_DE',
            'model' => 'NotificationContents',
            'foreign_key' => 1,
            'field' => 'email_html',
            'content' => 'Das Angebot mit der Nummer {{order.order_number}} wurde an den Kunden zur Freigabe geschickt.'
        ],
        [
            'id' => 2,
            'locale' => 'de_DE',
            'model' => 'NotificationContents',
            'foreign_key' => 2,
            'field' => 'email_html',
            'content' => 'Das Angebot mit der Nummer {{order.order_number}} wurde vom Kunden abgelehnt.'
        ],
        [
            'id' => 3,
            'locale' => 'de_DE',
            'model' => 'NotificationContents',
            'foreign_key' => 3,
            'field' => 'email_html',
            'content' => 'Das Angebot mit der Nummer {{order.order_number}} wurde vom Kunden angenommen.'
        ],
        [
            'id' => 4,
            'locale' => 'de_DE',
            'model' => 'NotificationContents',
            'foreign_key' => 4,
            'field' => 'email_html',
            'content' => 'Das Angebot mit der Nummer {{order.order_number}} wurde auf den Status {{orders.status}} geändert.'
        ],
        [
            'id' => 9,
            'locale' => 'de_DE',
            'model' => 'NotificationContents',
            'foreign_key' => 1,
            'field' => 'subject',
            'content' => 'Angebot zur Freigabe verschickt'
        ],
        [
            'id' => 10,
            'locale' => 'de_DE',
            'model' => 'NotificationContents',
            'foreign_key' => 2,
            'field' => 'subject',
            'content' => 'Angebot abgelehnt'
        ],
        [
            'id' => 11,
            'locale' => 'de_DE',
            'model' => 'NotificationContents',
            'foreign_key' => 3,
            'field' => 'subject',
            'content' => 'Angebot angenommen'
        ],
        [
            'id' => 12,
            'locale' => 'de_DE',
            'model' => 'NotificationContents',
            'foreign_key' => 4,
            'field' => 'subject',
            'content' => 'Angebot Status geändert'
        ],
        [
            'id' => 13,
            'locale' => 'de_DE',
            'model' => 'NotificationContents',
            'foreign_key' => 1,
            'field' => 'email_text',
            'content' => 'Das Angebot mit der Nummer {{order.order_number}} wurde an den Kunden zur Freigabe geschickt.'
        ],
        [
            'id' => 14,
            'locale' => 'de_DE',
            'model' => 'NotificationContents',
            'foreign_key' => 2,
            'field' => 'email_text',
            'content' => 'Das Angebot mit der Nummer {{order.order_number}} wurde vom Kunden abgelehnt.'
        ],
	];

}

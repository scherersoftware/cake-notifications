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

	];

}

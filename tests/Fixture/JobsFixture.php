<?php
namespace Notifications\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * JobsFixture
 *
 */
class JobsFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'queue' => ['type' => 'string', 'length' => 32, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'fixed' => null],
        'data' => ['type' => 'text', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'priority' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'expires_at' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'delay_until' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'locked' => ['type' => 'integer', 'length' => 1, 'unsigned' => false, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'attempts' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        '_indexes' => [
            'queue' => ['type' => 'index', 'columns' => ['queue', 'locked'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8_general_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => 2,
            'queue' => 'default',
            'data' => '{"queue":"default","class":["\\\\Notifications\\\\Transport\\\\EmailTransport","processQueueObject"],"args":[{"email":"a:11:{s:10:\\"viewConfig\\";a:3:{s:7:\\"_layout\\";s:7:\\"default\\";s:10:\\"_className\\";s:14:\\"Cake\\\\View\\\\View\\";s:8:\\"_helpers\\";a:1:{i:0;s:4:\\"Html\\";}}s:3:\\"_to\\";a:1:{s:15:\\"me@cleptric.com\\";s:15:\\"me@cleptric.com\\";}s:5:\\"_from\\";a:1:{s:27:\\"testing@scherer-sfotware.de\\";s:27:\\"testing@scherer-sfotware.de\\";}s:8:\\"_subject\\";s:4:\\"test\\";s:12:\\"_emailFormat\\";s:4:\\"text\\";s:13:\\"_emailPattern\\";s:59:\\"\\/^((?:[\\\\p{L}0-9.!#$%&\'*+\\\\\\/=?^_`{|}~-]+)*@[\\\\p{L}0-9-.]+)$\\/ui\\";s:7:\\"_domain\\";s:8:\\"cake.dev\\";s:10:\\"_messageId\\";b:1;s:11:\\"_appCharset\\";s:5:\\"UTF-8\\";s:7:\\"charset\\";s:5:\\"UTF-8\\";s:13:\\"headerCharset\\";s:5:\\"UTF-8\\";}","beforeSendCallback":[{"class":["App\\\\Lib\\\\Foo","bar1"],"args":["first_param","second_param"]},{"class":["App\\\\Lib\\\\Foo","bar1"],"args":["first_param1","second_param1"]}],"afterSendCallback":[],"locale":null}],"id":"0910fa36d4ce7cd0d8304e9544f0a878","queue_time":1476380400.77,"options":{"delay":11,"attempts_delay":600}}',
            'priority' => 0,
            'expires_at' => null,
            'delay_until' => '2016-10-13 17:40:11',
            'locked' => 0,
            'attempts' => 0
        ],
    ];
}

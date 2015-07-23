<?php
namespace Notifications\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;


/**
 * UserFixture
 *
 */
class UserFixture extends TestFixture {

/**
 * Fields
 *
 * @var array
 */
    public $fields = [
        'id' => ['type' => 'uuid', 'length' => null, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null],
        'status' => ['type' => 'integer', 'length' => 3, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'email' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'fixed' => null],
        'role' => ['type' => 'string', 'length' => 45, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'fixed' => null],
        'password' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'fixed' => null],
        'firstname' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'fixed' => null],
        'lastname' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'fixed' => null],
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
            'id' => 'f9df9eab-a6a3-4c89-9579-3eaeeb47e25f',
            'status' => 1,
            'email' => 'user1@familynet.dev',
            'role' => 'user',
            'password' => 'Lorem ipsum dolor sit amet',
            'firstname' => 'Lorem',
            'lastname' => 'ipsum',
            'created' => '2014-09-02 12:51:59',
            'modified' => '2014-09-02 12:51:59'
        ],
        [
            'id' => 'ecab3ebb-a0e5-46c0-81bd-7dee323bb903',
            'status' => 1,
            'email' => 'supplier1@familynet.dev',
            'role' => 'supplier',
            'password' => 'Lorem ipsum dolor sit amet',
            'firstname' => 'Lorem',
            'lastname' => 'ipsum',
            'created' => '2014-09-02 12:51:59',
            'modified' => '2014-09-02 12:51:59'
        ]
    ];

}

<?php
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Josegonzalez\CakeQueuesadilla\Queue\Queue;

if (!Plugin::loaded('Josegonzalez/CakeQueuesadilla')) {
    Plugin::load('Josegonzalez/CakeQueuesadilla');
    // Load Queue config
    Queue::setConfig(Configure::consume('Queuesadilla'));
}

if (!defined('SIGQUIT')) {
    define('SIGQUIT', 'SIGQUIT');
}
if (!defined('SIGTERM')) {
    define('SIGTERM', 'SIGTERM');
}
if (!defined('SIGINT')) {
    define('SIGINT', 'SIGINT');
}
if (!defined('SIGUSR1')) {
    define('SIGUSR1', 'SIGUSR1');
}

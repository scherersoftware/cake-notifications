<?php
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Josegonzalez\CakeQueuesadilla\Queue\Queue;

if (!Plugin::loaded('Josegonzalez/CakeQueuesadilla')) {
    Plugin::load('Josegonzalez/CakeQueuesadilla');
    // Load Queue config
    Queue::config(Configure::consume('Queuesadilla'));
}

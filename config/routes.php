<?php
use Cake\Routing\Router;

Router::plugin('Notifications', function ($routes) {
	$routes->fallbacks('DashedRoute');
});

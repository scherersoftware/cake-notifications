<?php
/**
 * Test suite bootstrap for Notifications.
 */
// Customize this to be a relative path for embedded plugins.
// For standalone plugins, this should point at a CakePHP installation.
$vendorPos = strpos(__DIR__, 'vendor/codekanzlei/cake-notifications');
if ($vendorPos !== false) {
    // Package has been cloned within another composer package, resolve path to autoloader
    $vendorDir = substr(__DIR__, 0, $vendorPos) . 'vendor/';
    $loader = require $vendorDir . 'autoload.php';
} else {
    // Package itself (cloned standalone)
    $loader = require __DIR__.'/../vendor/autoload.php';
}


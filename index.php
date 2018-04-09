<?php
/*
 * Goat v0.0.1
 */
//can be changed
date_default_timezone_set("UTC");

// Defines
define('ROOT_DIR', realpath(dirname(__FILE__)) .'/');
define('APP_DIR', ROOT_DIR .'private/');
define('VENDOR_DIR', ROOT_DIR .'vendor/');
define('WEB_BASE', sprintf("%s",pathinfo($_SERVER['PHP_SELF'])['dirname']));
define('WEB_DIR' , WEB_BASE . '/www/');
// Includes
session_start();
require(ROOT_DIR . 'vendor/inc/functions.php');
Goat\Generic::glob_recursive(ROOT_DIR . 'vendor/*.php');
$config = require(APP_DIR .'config/config.php');
// Define base URL
//define('BASE_URL', $config['base_url']);

return new Goat\goat($config);

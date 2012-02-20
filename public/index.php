<?php

/**
 * Set error reporting level & display_errors directive
 */
error_reporting(-1);
ini_set('display_errors', 'Off');

/**
 * Configure paths
 * (these constants are helpers and are not required by Fuel itself)
 */
define('DOCROOT', __DIR__.'/');
define('FUELPATH', DOCROOT.'../fuel/');
define('APPPATH', FUELPATH.'app/');

/**
 * Setup environment
 */
require FUELPATH.'kernel/classes/Environment.php';
use Fuel\Kernel\Environment;
$env = Environment::instance()->init(array(
	'name'      => isset($_SERVER['FUEL_ENV']) ? $_SERVER['FUEL_ENV'] : 'development',
	'locale'    => null,
	'language'  => 'en',
	'timezone'  => 'UTC',
	'encoding'  => 'UTF8',
	'paths'     => array(
		'docroot'  => DOCROOT,
		'fuel'     => FUELPATH,
		'app'      => APPPATH,
	),
));

/**
 * Initialize app
 */
use Fuel\Kernel\Application;
$app = Application::load('app', function($config) {
	$config->set(array(
		// 'prop' => 'val',
	));
});

/**
 * Run the app and output the response
 */
echo $app->request(Uri::current())->execute()->response();

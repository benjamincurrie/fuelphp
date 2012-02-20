<?php

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
	'name'  => isset($_SERVER['FUEL_ENV']) ? $_SERVER['FUEL_ENV'] : 'development',
	'path'  => FUELPATH,
));

/**
 * Initialize Application in package 'app'
 */
use Fuel\Kernel\Application\Base as Application;
$app = Application::load('app', function($config) {});

/**
 * Run the app and output the response
 */
echo $app->request(_env('input')->detect_uri())->execute()->response();

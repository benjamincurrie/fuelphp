<?php
include dirname(__DIR__).'/packages/fuel/bootstrap.php';

use Fuel\Foundation\Environment;
use Fuel\Foundation\Kernel;

Environment::is('development', function ()
{
	error_reporting(-1);
	ini_set('display_errors', 1);
});

Environment::is('production', function ()
{
	error_reporting(E_NONE);
	ini_set('display_errors', 0);
});

Environment::is('test', function ()
{
	error_reporting(-1);
	ini_set('display_errors', 1);
	include_once 'PHPUnit/Autoload.php';
});

/**
 * Define all of your project's applications
 */
Kernel::installed_apps(
	// All of your installed applications
	array(
		'main' => __DIR__.'/main',
	),

	// Applications to auto-boot
	array('main')
);

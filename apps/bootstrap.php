<?php
/**
 * Boot up the Fuel package
 */
include dirname(__DIR__).'/packages/fuel/bootstrap.php';

/**
 * Define all of your project's applications
 */
Fuel\Foundation\Kernel::define_apps(array(
	'main' => __DIR__.'/main',
));

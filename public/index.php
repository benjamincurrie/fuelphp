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
$app = Application::load('app', function() {});

/**
 * Run the app and output the response
 */
echo $app->request(_env('input')->uri())->execute()->response()->send_headers();

?>

<p>
	<strong>Time elapsed:</strong> <?php echo round(_env()->time_elapsed(), 5); ?> s<br />
	<strong>Memory usage:</strong> <?php echo round(_env()->mem_usage() / 1000000, 4); ?> MB<br />
	<strong>Peak memory usage:</strong> <?php echo round(_env()->mem_usage(true) / 1000000, 4); ?> MB
</p>

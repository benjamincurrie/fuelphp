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

// Uncomment the following line to enable the Fuel 1.x Legacy classes
// _loader()->add_global_ns_alias('Fuel\\Core\\Legacy');

/**
 * Initialize Application in package 'app'
 */
$app = _loader()->load_app('app', function() {});

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

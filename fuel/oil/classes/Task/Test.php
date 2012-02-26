<?php

namespace Fuel\Oil\Task;
use Classes;


class Test extends Classes\Task\Base
{
	public function before()
	{
		parent::before();

		// Include PHPUnit when available (suppress error, will be thrown below when class isn't found)
		@include_once 'PHPUnit/Autoload.php';

		// Check if PHPUnit is available
		if ( ! class_exists('PHPUnit_Framework_TestCase'))
		{
			throw new \RuntimeException('PHPUnit does not appear to be installed.'.PHP_EOL.PHP_EOL."\tPlease visit http://phpunit.de and install.");
		}
	}

	public function action_index()
	{
		// CD to the root of Fuel and call up phpunit with a path to our config
		$command = 'phpunit -c "'._env()->path('fuel').'phpunit.xml"';

		// Respect the group option
		$this->cli->option('group') and $command .= ' --group '.$this->cli->option('group');

		// Respect the coverage-html option
		$this->cli->option('coverage-html') and $command .= ' --coverage-html '.$this->cli->option('coverage-html');

		$this->cli->write('Tests Running... This may take a few moments.', 'green');

		foreach(explode(';', $command) as $c)
		{
			passthru($c);
		}
	}
}

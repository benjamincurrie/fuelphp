<?php

namespace Fuel\Oil\Task;
use Classes;

class Main extends Classes\Task\Base
{
	/**
	 * Returns the Fuel version
	 *
	 * @return  string
	 */
	public function action_version()
	{
		return 'FuelPHP '.\Fuel\Kernel\Environment::VERSION;
	}

	/**
	 * Returns instructions for using the CLI
	 *
	 * @return  \Fuel\Kernel\View\Viewable
	 */
	public function action_help()
	{
		return $this->app->forge('View', 'main/help');
	}
}

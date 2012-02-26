<?php

namespace Fuel\Oil\Controller;
use Classes;

class Main extends Classes\Controller\Cli
{
	/**
	 * Returns the Fuel version
	 *
	 * @return  string
	 */
	public function version()
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

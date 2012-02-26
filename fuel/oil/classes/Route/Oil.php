<?php

namespace Fuel\Oil\Route;
use Fuel\Kernel\Application;
use Classes;

class Oil extends Classes\Route\Fuel
{
	/**
	 * @var  \Fuel\Kernel\Application\Base
	 */
	protected $app;

	/**
	 * @var  \Fuel\Kernel\Cli
	 */
	protected $cli;

	/**
	 * @var  array  short forms for controller names
	 */
	protected $aliases = array(
		'c'     => 'console',
		'cell'  => 'cells',
		'g'     => 'generate',
		'r'     => 'refine',
		't'     => 'test',
	);

	public function __construct() {}

	/**
	 * Magic Fuel method that is the setter for the current app
	 *
	 * @param  \Fuel\Kernel\Application\Base  $app
	 */
	public function _set_app(Application\Base $app)
	{
		$this->app = $app;
		$this->cli = $this->app->get_object('Cli');
	}

	public function matches($uri)
	{
		// This route only works when requesting '__commandline'
		if ($uri != '/__commandline')
		{
			return false;
		}

		// Allow -help/(empty) for helptext and -v/-version to get the Fuel version
		if (in_array($this->cli->option(1), array('-v', '-version', '-help', null)))
		{
			if ($this->cli->option('v', $this->cli->option('version')))
			{
				return $this->parse('main/version');
			}
			return $this->parse('main/help');
		}

		// Get the intended Controller
		$controller = $this->cli->option(1);
		isset($this->aliases[$controller]) and $controller = $this->aliases[$controller];

		// Attempt to find the Controller
		if ($this->parse($controller))
		{
			return true;
		}

		// On failure: report it and show help text
		$this->cli->write('Error: controller for command "'.$controller.'" not found.');
		return  $this->parse('main/help');
	}

	/**
	 * Clear arguments from input
	 *
	 * @param   array  $actions
	 * @return  array
	 */
	protected function _clear_args($actions = array())
	{
		foreach ($actions as $key => $action)
		{
			if (substr($action, 0, 1) === '-')
			{
				unset($actions[$key]);
			}
		}

		return $actions;
	}
}

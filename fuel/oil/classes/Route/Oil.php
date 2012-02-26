<?php

namespace Fuel\Oil\Route;
use Fuel\Kernel\Application;
use Classes;

class Oil extends Classes\Route\Base
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

	/**
	 * @var  callback  something callable that matched
	 */
	protected $match;

	/**
	 * @var  array  URI segments
	 */
	protected $segments = array();

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
		if ($this->parse(ucfirst($controller)))
		{
			return true;
		}

		// On failure: report it and show help text
		$this->cli->write('Error: controller for command "'.$controller.'" not found.');
		return  $this->parse('main/help');
	}

	/**
	 * Attempts to find the controller and returns success
	 *
	 * @return  bool
	 */
	protected function parse($task)
	{
		// Return Controller when found
		if ($task = $this->find_task($task))
		{
			$this->match = array($this->app->forge($task), 'router');
			return true;
		}

		// Failure...
		return false;
	}

	/**
	 * Parses the URI into a controller class
	 *
	 * @param   $uri
	 * @return  string|bool
	 */
	protected function find_task($uri)
	{
		if ($task = $this->app->find_class('Task', $uri))
		{
			return $task;
		}
		return false;
	}

	/**
	 * Return an array with 1. callable to be the task and 2. additional params array
	 *
	 * @return  array(callback, params)
	 */
	public function match()
	{
		return array($this->match, $this->segments);
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

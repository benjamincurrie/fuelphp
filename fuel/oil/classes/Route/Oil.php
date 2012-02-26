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
		if ($uri != '__commandline')
		{
			return false;
		}

		// Remove flag options from the main argument list
		$args = $this->app->active_request()->input->server('argv');

		if ( ! isset($args[1]))
		{
			if ($this->cli->option('v', $this->cli->option('version')))
			{
				return $this->parse('main/version');
			}
			return $this->parse('main/help');
		}

		isset($this->aliases[$args[1]]) and $args[1] = $this->aliases[$args[1]];

		return $this->parse($args[1]) or $this->parse('main/help');
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

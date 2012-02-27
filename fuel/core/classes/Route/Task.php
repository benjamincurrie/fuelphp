<?php

namespace Fuel\Core\Route;
use Fuel\Kernel\Application;
use Classes;

class Task extends Classes\Route\Fuel
{
	/**
	 * @var  \Fuel\Kernel\Cli
	 */
	protected $cli;

	/**
	 * @var  array  URI segments
	 */
	protected $segments = array();

	/**
	 * Magic Fuel method that is the setter for the current app
	 *
	 * @param  \Fuel\Kernel\Application\Base  $app
	 */
	public function _set_app(Application\Base $app)
	{
		parent::_set_app($app);
		_env('is_cli') and $this->cli = $this->app->get_object('Cli');
	}

	public function matches($uri)
	{
		// Disable running these routes outside the commandline
		if (is_null($this->cli))
		{
			return false;
		}

		if (parent::matches($uri))
		{
			return true;
		}

		// When the search matches the translation this is the hail mary attempt
		if ($this->search == $this->translation)
		{
			$this->cli->write('Error: controller for command "'.$uri.'" not found.');
			return  $this->parse('main/help');
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
	protected function find_class($uri)
	{
		$uri_array = explode('/', trim($uri, '/'));
		while ($uri_array)
		{
			if ($controller = $this->app->find_class('Task', implode('/', $uri_array)))
			{
				return $controller;
			}
			array_unshift($this->segments, array_pop($uri_array));
		}
		return false;
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

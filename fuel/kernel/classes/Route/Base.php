<?php

namespace Fuel\Kernel\Route;
use Fuel\Kernel\Application;

abstract class Base
{
	/**
	 * @var  \Fuel\Kernel\Request\Base
	 */
	protected $request;

	/**
	 * @var  \Fuel\Kernel\Application\Base
	 */
	protected $app;

	/**
	 * Magic Fuel method that is the setter for the current app
	 *
	 * @param  \Fuel\Kernel\Application\Base  $app
	 */
	public function _set_app(Application\Base $app)
	{
		$this->app      = $app;
		$this->request  = $app->active_request();
	}

	/**
	 * Checks if the uri matches this route
	 *
	 * @param   string  $uri
	 * @return  bool    whether it matched
	 */
	abstract public function matches($uri);

	/**
	 * Return a callable to be the controller
	 *
	 * @return  callable
	 */
	abstract public function match();
}

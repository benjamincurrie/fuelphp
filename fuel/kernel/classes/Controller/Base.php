<?php

namespace Fuel\Kernel\Controller;
use Fuel\Kernel\Application;
use Fuel\Kernel\Request;
use Fuel\Kernel\Response;

abstract class Base
{
	/**
	 * @var  string  default method to call on empty action input
	 */
	protected static $default_action = 'index';

	/**
	 * @var  string  required prefix for method to be accessible as action
	 */
	protected static $action_prefix = 'action_';

	/**
	 * @var  \Fuel\Kernel\Application\Base
	 */
	public $app;

	/**
	 * @var  \Fuel\Kernel\Loader\Loadable
	 */
	public $loader;

	/**
	 * Magic Fuel method that is the setter for the current app
	 *
	 * @param  \Fuel\Kernel\Application\Base  $app
	 */
	public function _set_app(Application\Base $app)
	{
		$this->app = $app;
	}

	public function router(array $args)
	{
		// Determine the method
		$method = static::$action_prefix.(array_shift($args) ?: static::$default_action);

		// Return false if it doesn't exist
		if ( ! method_exists($this, $method))
		{
			throw new Request\Exception_404('No such action "'.$method.'" in Controller: '.get_class($this));
		}

		/**
		 * Return false if the method isn't public
		 */
		$method = new \ReflectionMethod($this, $method);
		if ( ! $method->isPublic())
		{
			throw new Request\Exception_404('Unavailable action "'.$method.'" in Controller: '.get_class($this));
		}

		$this->before();
		$response = $method->invokeArgs($this, $args);
		$response = $this->after($response);

		return $response;
	}

	/**
	 * Method to execute for controller setup
	 */
	public function before() {}

	/**
	 * Method to execute for finishing up controller execution, ensures the response is a Response object
	 *
	 * @param   mixed  $response
	 * @return  \Fuel\Kernel\Response\Base
	 */
	public function after($response)
	{
		if ( ! $response instanceof Response\Base)
		{
			$response = $this->app->forge('Response', $response);
		}

		return $response;
	}
}

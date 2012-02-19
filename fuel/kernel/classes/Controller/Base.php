<?php

namespace Fuel\Kernel\Controller;
use Fuel\Kernel\Application;
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
	 * @var  Application
	 */
	public $app;

	/**
	 * @var  \Fuel\Kernel\Loader\Base
	 */
	public $loader;

	public function __construct(Application $app)
	{
		$this->app     = $app;
		$this->loader  = $app->loader;
	}

	public function router(array $args)
	{
		// Determine the method
		$method = static::$action_prefix.(array_shift($args) ?: static::$default_action);

		// Return false if it doesn't exist
		if ( ! method_exists($this, $method))
		{
			return false;
		}

		/**
		 * Return false if the method isn't public
		 */
		$method = new \ReflectionMethod($this, $method);
		if ( ! $method->isPublic())
		{
			return false;
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
			$this->app->forge('Response', $response);
		}

		return $response;
	}
}

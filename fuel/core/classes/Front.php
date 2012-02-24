<?php

namespace Fuel\Core;

abstract class Front
{
	/**
	 * @var  string  (required) Overwrite for the classname this front represents
	 */
	// protected static $classname = '';

	/**
	 * Forges an object for this front
	 *
	 * @return  object
	 */
	public static function forge()
	{
		$args = func_get_args();
		$name = array_shift($args);
		array_unshift($args, static::$classname);

		$obj = call_user_func_array(array(_env(), 'forge'), $args);
		_env()->set_object(static::$classname, $name, $obj);

		return $obj;
	}

	/**
	 * Fetches an instance for this front
	 *
	 * @param   string  $name
	 * @return  object
	 */
	public static function instance($name = null)
	{
		return _env()->get_object(static::$classname, $name);
	}

	/**
	 * Support for usage of all dynamic methods on the driver
	 *
	 * @param   string  $method
	 * @param   array   $args
	 * @return  mixed
	 * @throws  \BadMethodCallException
	 */
	public static function __callStatic($method, array $args)
	{
		$self = static::instance();
		if ( ! is_callable(array($self, $method)))
		{
			throw new \BadMethodCallException('No such method available on: '.static::$classname);
		}

		return call_user_func_array(array($self, $method), $args);
	}

	/**
	 * Prevent instantiation
	 */
	private function __construct() {}
}

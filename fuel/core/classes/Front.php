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
		array_unshift($args, static::$classname);
		return call_user_func_array(array(_env(), 'forge'), $args);
	}

	/**
	 * Fetches an instance for this front
	 *
	 * @param   string  $name
	 * @return  object
	 */
	public static function instance($name = 'default')
	{
		try
		{
			$class = static::$classname;
			$object = _env()->get_object($class, $name);
		}
		catch (\RuntimeException $e)
		{
			if ($name == 'default')
			{
				$object = _env()->forge($class);
				_env('dic')->set_object($class, $object);
			}
			else
			{
				throw $e;
			}
		}

		return $object;
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

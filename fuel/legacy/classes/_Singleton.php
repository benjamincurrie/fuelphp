<?php

namespace Fuel\Legacy;

// As abstract static functions are forbidden:
interface _Instance_Interface
{
	/**
	 * Fetches an instance for this front
	 *
	 * @param   string  $name
	 * @return  object
	 */
	public static function instance();
}

abstract class _Singleton implements _Instance_Interface
{
	/**
	 * @var  string  (required) Overwrite for the classname this front represents
	 */
	// protected static $classname = '';

	/**
	 * @var  array  Maps specific method names to callbacks for legacy support
	 */
	// protected static $method_mapping = array();

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
		// Allow old method name to map to new one
		if (isset(static::$method_mapping[$method]))
		{
			$callback = static::$method_mapping[$method];

			// When single value in array: add the current instance as the object
			if (is_array($callback) and count($callback) === 1)
			{
				array_unshift($callback, static::instance());
			}
		}
		// Default to same method name
		else
		{
			$callback = array(static::instance(), $method);
		}

		if ( ! is_callable($callback))
		{
			throw new \BadMethodCallException('No such method available on: '.static::$classname);
		}

		return call_user_func_array($callback, $args);
	}

	/**
	 * Prevent instantiation
	 */
	private function __construct() {}
}

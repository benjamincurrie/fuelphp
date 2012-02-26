<?php

namespace Fuel\Legacy;

abstract class _Multiton extends _Singleton
{
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
}

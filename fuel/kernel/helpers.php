<?php

/**
 * Fetch the Fuel Environment
 *
 * @return  Fuel\Kernel\Environment
 */
function _env()
{
	return Fuel\Kernel\Environment::instance();
}

/**
 * Fetch the Fuel loader
 *
 * @return  Fuel\Kernel\Loader
 */
function _loader()
{
	return _env()->loader;
}

/**
 * Forge an object
 *
 * @return  object
 */
function _forge()
{
	return call_user_func_array(array(_loader(), 'forge'), func_get_args());
}

/**
 * Set a value on an array according to a dot-notated key
 *
 * @param   string              $key
 * @param   array|\ArrayAccess  $data
 * @param   bool                $setting
 */
function set_dots_to_array($key, &$input, $setting)
{
	$data =& $input;

	// Explode the key and start iterating
	$keys = explode('.', $key);
	while (count($keys) > 1)
	{
		$key = array_shift($keys);
		if ( ! isset($data[$key])
			or ( ! is_array($data[$key]) and ! $data[$key] instanceof \ArrayAccess))
		{
			// Create new subarray or overwrite non array
			$data[$key] = array();
		}
		$data =& $data[$key];
	}

	// Set when this is a set operation
	if ( ! is_null($setting))
	{
		$data = $setting;
	}
}

/**
 * Get a value from an array according to a dot-notated key
 *
 * @param   string              $key
 * @param   array|\ArrayAccess  $data
 * @param   mixed               $return
 * @return  bool
 */
function get_dots_to_array($key, &$input, &$return)
{
	$return =& $input;

	// Explode the key and start iterating
	$keys = explode('.', $key);
	while (count($keys) > 1)
	{
		$key = array_shift($keys);
		if ( ! isset($return[$key])
			or ( ! is_array($return[$key]) and ! $return[$key] instanceof \ArrayAccess))
		{
			// Value not found, return failure
			return false;
		}
		$return =& $return[$key];
	}

	// return success
	return true;
}
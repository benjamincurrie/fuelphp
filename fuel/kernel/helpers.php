<?php

/**
 * Fetch the Fuel Environment
 *
 * @param   null|string  $var
 * @return  mixed
 */
function _env($var = null)
{
	if ($var)
	{
		return Fuel\Kernel\Environment::instance()->{$var};
	}

	return Fuel\Kernel\Environment::instance();
}

/**
 * Return the current active Application
 *
 * @param   null|string  $var
 * @return  mixed
 */
function _app($var = null)
{
	$app = _env()->active_app();

	if ( ! $app)
	{
		return null;
	}

	return $var ? $app->{$var} : $app;
}


/**
 * Return the current active Request
 *
 * @param   null|string  $var
 * @return  mixed
 */
function _req($var = null)
{
	$req = ($app = _app()) ? $app->active_request() : null;

	if ( ! $req)
	{
		return null;
	}

	return $var ? $req->{$var} : $req;
}

/**
 * Fetch the Fuel loader
 *
 * @return  Fuel\Kernel\Loader
 */
function _loader()
{
	return _env('loader');
}

/**
 * Forge an object
 *
 * @return  object
 */
function _forge()
{
	return call_user_func_array(array(_app() ?: _env(), 'forge'), func_get_args());
}

/**
 * Set a value on an array according to a dot-notated key
 *
 * @param   string              $key
 * @param   array|\ArrayAccess  $data
 * @param   bool                $setting
 */
function array_set_dot_key($key, &$input, $setting)
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
function array_get_dot_key($key, &$input, &$return)
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

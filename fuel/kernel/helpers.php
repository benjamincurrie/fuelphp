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
 * Translates a dot notated key to a value from the given data.
 * It returns success of the operation, actual value found is returned as a reference
 *
 * @param   string              $key
 * @param   array|\ArrayAccess  $data
 * @param   mixed               $return
 * @param   bool                $setting
 * @return  bool
 */
function dots_to_array($key, &$data, &$return)
{
	// When the return is provided this is a set operation
	$setting  = $return;
	// Make the return var the data now
	$return   = $data;

	// Explode the key and start iterating
	$keys = explode('.', $key);
	while (count($keys) > 1)
	{
		$key = array_shift($keys);
		if ( ! isset($return[$key])
			or ( ! is_array($return[$key]) and ! $return[$key] instanceof \ArrayAccess))
		{
			if (is_null($setting))
			{
				// Value not found, return failure
				return false;
			}
			else
			{
				// Create new subarray or overwrite non array
				$return[$key] = array();
			}
		}
		$return =& $return[$key];
	}

	// Set when this is a set operation
	if ( ! is_null($setting))
	{
		$return = $setting;
	}

	// return success
	return true;
}
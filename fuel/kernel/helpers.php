<?php

/**
 * Fetch the Fuel Environment
 *
 * @return  Fuel\Kernel\Environment
 */
function __env()
{
	return Fuel\Kernel\Environment::instance();
}

/**
 * Fetch the Fuel loader
 *
 * @return  Fuel\Kernel\Loader
 */
function __loader()
{
	return Fuel\Kernel\Environment::instance()->loader;
}

/**
 * Forge an object
 *
 * @return  object
 */
function __forge()
{
	return call_user_func_array(array(__loader(), 'forge'), func_get_args());
}
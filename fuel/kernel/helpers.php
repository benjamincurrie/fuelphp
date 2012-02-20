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
<?php

namespace Fuel\Legacy;

class Input extends _Singleton
{
	protected static $classname = 'Input';

	protected static $method_mapping = array(
		'delete' => array('param'),
		'get' => array('query_string'),
		'post' => array('param'),
		'put' => array('param'),
		'get_post' => array('param'),
		'all' => 'Fuel\\Core\\Front\\Input::all',
	);

	/**
	 * @return  \Fuel\Kernel\Input
	 */
	public static function instance()
	{
		return _env('input');
	}

	/**
	 * Returns a merged input of query string (GET) and input params (POST, PUT, DELETE)
	 *
	 * @return  array
	 */
	public static function all()
	{
		$instance = static::instance();

		if ($instance->method() == 'GET')
		{
			return $instance->param();
		}

		return array_merge($instance->query_string(), $instance->param());
	}
}
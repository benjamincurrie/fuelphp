<?php

namespace Fuel\Kernel\DiC;

interface Container
{
	/**
	 * @param  \Fuel\Kernel\Application\Base  $app
	 * @param  \Fuel\Kernel\DiC\Container     $parent
	 */
	public function __construct($app = null, $parent = null);

	/**
	 * Add a class to use for a given classname
	 *
	 * @param   string  $class
	 * @param   string  $actual
	 * @return  Container
	 */
	public function set_class($classname, $actual);

	/**
	 * Add multiple classes to use for classnames
	 *
	 * @param   array  $classes
	 * @return  Container
	 */
	public function set_classes(array $classenames);

	/**
	 * Get an actual class for a given classname
	 *
	 * @param   string  $class
	 * @return  string
	 */
	public function get_class($class);

	/**
	 * Create an object of a given classname
	 *
	 * @param   string  $class
	 * @return  object
	 */
	public function forge($classname);

	/**
	 * Add an object to the container
	 *
	 * @param   string  $class
	 * @param   string  $name
	 * @param   object  $instance
	 * @return  Container
	 */
	public function set_object($classname, $name, $instance);

	/**
	 * Fetch an object from the container
	 *
	 * @param   string  $class
	 * @param   string  $name
	 * @return  object
	 */
	public function get_object($classname, $name);
}

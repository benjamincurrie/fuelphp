<?php

namespace Fuel\Core\Loader;
use Classes\Loader\Package;

class Closure extends Package
{
	/**
	 * @var  \Closure
	 */
	protected $loader;

	/**
	 * Uses a closure to translate the classname to a path
	 *
	 * @param $class
	 * @return string
	 */
	public function class_to_path($fullname, $class, $basepath)
	{
		return call_user_func_array($this->loader, array($fullname, $class, $basepath));
	}

	/**
	 * Change the type of class loader used, by default 'psr' and 'fuelv1' included
	 *
	 * @param   \Closure  $loader
	 * @return  Closure
	 * @throws  \RuntimeException
	 */
	public function set_loader(\Closure $loader)
	{
		$this->loader = $loader;
		return $this;
	}
}

<?php

namespace Fuel\Core\Loader;
use Classes\Loader\Package;

class Lowercase extends Package
{
	/**
	 * Makes the PSR-0 path fully lowercase
	 *
	 * @param   string  $fullname  full classname
	 * @param   string  $class     classname relative to base/module namespace
	 * @param   string  $basepath
	 * @return  string
	 */
	public function class_to_path($fullname, $class, $basepath)
	{
		return $basepath.substr(strtolower(parent::class_to_path($fullname, $class, $basepath)), strlen($basepath));
	}
}

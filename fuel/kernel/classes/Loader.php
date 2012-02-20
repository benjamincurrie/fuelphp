<?php

namespace Fuel\Kernel;
use Fuel\Kernel\Environment;

class Loader
{
	const TYPE_APP = 0;
	const TYPE_PACKAGE = 1000;
	const TYPE_CORE = 100000;

	/**
	 * @var  array  active loaders in a prioritized list
	 */
	protected $packages = array(
		static::TYPE_APP      => array(),
		static::TYPE_PACKAGE  => array(),
		static::TYPE_CORE     => array(),
	);

	/**
	 * @var  string  classname of the class currently being loaded
	 */
	protected $__current_class_load = '';

	/**
	 * Adds a package
	 *
	 * @param   string  $name
	 * @param   int     $type
	 * @return  Loader\Base  for method chaining
	 * @throws  \RuntimeException
	 */
	public function load_package($name, $type = static::TYPE_PACKAGE)
	{
		! is_array($name) and $name = array($name, Environment::instance()->path('fuel').$name.'/');
		list($name, $path) = $name;

		if (isset($this->packages[$type][$name]))
		{
			throw new \RuntimeException('Package already loaded, can\'t be loaded twice.');
		}

		$loader = require $path.'loader.php';
		if ( ! $loader instanceof Loader\Package)
		{
			throw new \RuntimeException('Package loader must implement Fuel\\Kernel\\Loader\\Base');
		}

		$this->packages[$type][$name] = $loader;
		return $loader;
	}

	/**
	 * Fetch a specific package
	 *
	 * @param   string  $name
	 * @param   int     $type
	 * @return  Loader\Base
	 * @throws  \OutOfBoundsException
	 */
	public function package($name, $type = static::TYPE_PACKAGE)
	{
		if ( ! isset($this->packages[$type][$name]))
		{
			throw new \OutOfBoundsException('Unknown package: '.$name);
		}
		return $this->packages[$type][$name];
	}

	/**
	 * Fetch all packages or just those of a specific type
	 *
	 * @param   int|null  $type  null for all, int for a specific type
	 * @return  array
	 * @throws  \OutOfBoundsException
	 */
	public function packages($type = null)
	{
		if (is_null($type))
		{
			return $this->packages;
		}
		elseif ( ! isset($this->packages[$type]))
		{
			throw new \OutOfBoundsException('Unknown package type: '.$type);
		}

		return $this->packages[$type];
	}

	/**
	 * Attempts to load a class from a package
	 *
	 * @param   string  $class
	 * @return  bool
	 */
	public function load_class($class)
	{
		$class = ltrim('\\', $class);

		if (empty($this->__current_class_load))
		{
			$this->__current_class_load = $class;
		}

		try
		{
			foreach ($this->packages as $pkgs)
			{
				foreach ($pkgs as $pkg)
				{
					if ($pkg->load_class($class))
					{
						$this->init_class($class);
						return true;
					}
				}
			}
		}
		catch (\Exception $e)
		{
			$this->__current_class_load = null;
			throw $e;
		}

		// @deprecated  for Fuel 1.x BC, only works when an app is running
		$env = Environment::instance();
		if ($env->__get('global_core_alias')
			and (($app = $env->active_app()) and $actual = $app->get_class($class)))
		{
			class_alias($actual, $class);
			$this->__current_class_load = null;
			return true;
		}

		if ($this->__current_class_load == $class)
		{
			$this->__current_class_load = null;
		}

		return false;
	}

	/**
	 * Initializes a class when it's the requested one and has a static _init() method
	 *
	 * @param   string  $class
	 * @return  void
	 */
	protected function init_class($class)
	{
		if ($this->__current_class_load == $class)
		{
			$this->__current_class_load == null;
			if (method_exists($class, '_init'))
			{
				call_user_func($class.'::_init');
			}
		}
	}
}

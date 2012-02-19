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
	 * @var  array  classnames and their 'translation'
	 */
	protected $dic_classes = array();

	/**
	 * @var  array  named instances organized by classname
	 */
	protected $dic_instances = array();

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

		// @deprecated  for Fuel 1.x BC
		if (Environment::instance()->global_core_alias and $this->load_class($actual = $this->get_dic_class($class)))
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

	/**
	 * Attempts to find one or more files in the packages
	 *
	 * @param   string  $location
	 * @param   string  $file
	 * @param   bool    $multiple
	 * @return  array|bool
	 */
	public function find_file($location, $file, $multiple = false)
	{
		$return = $multiple ? array() : false;

		foreach ($this->packages as $pkgs)
		{
			foreach ($pkgs as $pkg)
			{
				if ($file = $pkg->find_file($location, $file))
				{
					if ( ! $multiple)
					{
						return $file;
					}
					$return[] = $file;
				}
			}
		}

		if ($multiple)
		{
			return $return;
		}

		return false;
	}

	/**
	 * Find multiple files using find_file() method
	 *
	 * @param $location
	 * @param $file
	 * @return array|bool
	 */
	public function find_files($location, $file)
	{
		return $this->find_file($location, $file, true);
	}

	/**
	 * Set class that is fetched from the dic classes property
	 *
	 * @param   string  $class
	 * @param   string  $actual
	 * @return  Loader  to allow method chaining
	 */
	public function set_dic_class($class, $actual)
	{
		$this->set_dic_classes(array($class => $actual));
		return $this;
	}

	/**
	 * Set classes that are fetched from the dic classes property
	 *
	 * @param   array   $classes
	 * @return  Loader  to allow method chaining
	 */
	public function set_dic_classes(array $classes)
	{
		foreach ($classes as $class => $actual)
		{
			$this->dic_classes[$class] = $actual;
		}
		return $this;
	}

	/**
	 * Translates a classname to the one set in the DiC classes property
	 *
	 * @param   string  $class
	 * @return  string
	 */
	public function get_dic_class($class)
	{
		if (isset($this->dic_classes[$class]))
		{
			return $this->dic_classes[$class];
		}
		return $class;
	}

	/**
	 * Forges a new object for the given class, supporting DI replacement
	 *
	 * @param   string  $class
	 * @return  object
	 */
	public function forge($class)
	{
		$reflection = new \ReflectionClass($this->get_dic_class($class));
		return $reflection->newInstanceArgs(array_slice(func_get_args(), 1));
	}

	/**
	 * Register an instance with the DiC
	 *
	 * @param   string  $class
	 * @param   string  $name
	 * @param   object  $instance
	 * @return  Loader
	 */
	protected function set_dic_instance($class, $name, $instance)
	{
		$this->dic_instances[$class][$name] = $instance;
		return $this;
	}

	/**
	 * Fetch an instance from the DiC
	 *
	 * @param   string  $class
	 * @param   string  $name
	 * @return  object
	 * @throws  \RuntimeException
	 */
	public function get_dic_instance($class, $name)
	{
		if ( ! isset($this->dic_instances[$class][$name]))
		{
			throw new \RuntimeException('Instance name "'.$name.'" not registered for class: '.$class);
		}
		return $this->dic_instances[$class][$name];
	}
}

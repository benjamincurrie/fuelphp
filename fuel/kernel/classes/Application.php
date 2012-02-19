<?php

namespace Fuel\Kernel;

abstract class Application
{
	/**
	 * @var  array  appnames and their classnames
	 */
	protected static $_apps = array();

	/**
	 * Register a new app classname
	 *
	 * @param  string  $appname    Given name for an application
	 * @param  string  $classname  Classname for the application
	 */
	public static function register($appname, $classname)
	{
		static::$_apps[$appname] = $classname;
	}

	/**
	 * Serve the application as configured the name
	 *
	 * @param   string   $appname
	 * @param   Closure  $config
	 * @return  Application
	 * @throws  \OutOfBoundsException
	 */
	public static function load($appname, \Closure $config)
	{
		$loader = Loader::instance()->load_package($appname, Loader::TYPE_APP)
		$loader->set_routable(true);

		if ( ! isset(static::$_apps[$appname]))
		{
			throw new \OutOfBoundsException('Unknown Appname.');
		}

		$class = static::$_apps[$appname];
		return new $class($config, $loader);
	}

	/**
	 * @var  Loader\Base  the Application's own loader instance
	 */
	protected $loader;

	/**
	 * @var  array  packages to load
	 */
	protected $packages = array();

	/**
	 * @var  Request  contains the request object once created
	 */
	protected $request;

	/**
	 * @var  Response  contains the response object after execution
	 */
	protected $response;

	/**
	 * @var  array  classnames and their 'translation'
	 */
	protected $dic_classes = array();

	/**
	 * @var  array  named instances organized by classname
	 */
	protected $dic_instances = array();

	public function __construct(\Closure $config, Loader\Base $loader)
	{
		$this->loader = $loader;

		foreach ($this->packages as $pkg)
		{
			try
			{
				Loader::instance()->load_package($pkg, Loader::TYPE_PACKAGE);
			}
			// ignore exception thrown for double package load
			catch (\RuntimeException $e) {}
		}
	}

	/**
	 * Create the application main request
	 *
	 * @param   string  $uri
	 * @return  Request
	 */
	public function request($uri)
	{
		$this->request = Loader::instance()->forge('Request', $uri);
		return $this;
	}

	/**
	 * Execute the application main request
	 *
	 * @return  Application
	 */
	public function execute()
	{
		$this->response = $this->request->execute();
		return $this;
	}

	/**
	 * Return the response object
	 *
	 * @return  Response
	 */
	public function response()
	{
		return $this->response->send_headers();
	}

	/**
	 * Locate the controller
	 *
	 * @param   string  $controller
	 * @return  bool|string  the controller classname or false on failure
	 */
	public function find_controller($controller)
	{
		// First attempt the package
		if ($found = $this->loader->find_controller($controller))
		{
			return $found;
		}

		// if not found attempt loaded packages
		foreach ($this->packages as $pkg)
		{
			is_array($pkg) and $pkg = reset($pkg);
			if ($found = Loader::instance()->package($pkg)->find_controller($controller))
			{
				return $found;
			}
		}

		// all is lost
		return false;
	}

	/**
	 * Set class that is fetched from the dic classes property
	 *
	 * @param   string  $class
	 * @param   string  $actual
	 * @return  Application  to allow method chaining
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
	 * @return  Application  to allow method chaining
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

		return Loader::instance()->get_dic_class($class);
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
	 * @return  Application
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
	protected function get_dic_instance($class, $name)
	{
		if ( ! isset($this->dic_instances[$class][$name]))
		{
			return Loader::instance()->get_dic_instance($class, $name);
		}
		return $this->dic_instances[$class][$name];
	}
}

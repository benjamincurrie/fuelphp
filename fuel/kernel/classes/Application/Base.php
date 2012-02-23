<?php

namespace Fuel\Kernel\Application;
use Fuel\Kernel\DiC;
use Fuel\Kernel\Loader;
use Fuel\Kernel\Request;
use Fuel\Kernel\Route;

abstract class Base
{
	/**
	 * Serve the application as configured the name
	 *
	 * @param   string   $appname
	 * @param   Closure  $config
	 * @return  Base
	 * @throws  \OutOfBoundsException
	 */
	public static function load($appname, \Closure $config)
	{
		$loader = _loader()->load_package($appname, Loader::TYPE_APP);
		$loader->set_routable(true);

		$class = _env()->app_class($appname);
		return new $class($config, $loader);
	}

	/**
	 * @var  \Fuel\Kernel\Loader\Loadable  the Application's own loader instance
	 */
	protected $loader;

	/**
	 * @var  array  route objects
	 */
	protected $routes = array();

	/**
	 * @var  array  packages to load
	 */
	protected $packages = array();

	/**
	 * @var  \Fuel\Kernel\Request\Base  contains the app main request object once created
	 */
	protected $request;

	/**
	 * @var  \Fuel\Kernel\Request\Base  current active Request, not necessarily the main request
	 */
	protected $active_request;

	/**
	 * @var  Base  active Application before activation of this one
	 */
	protected $_before_activate;

	/**
	 * @var  \Fuel\Kernel\DiC\Dependable
	 */
	protected $dic;

	public function __construct(\Closure $config, Loader\Loadable $loader)
	{
		$this->loader = $loader;

		foreach ($this->packages as $pkg)
		{
			try
			{
				_loader()->load_package($pkg, Loader::TYPE_PACKAGE);
			}
			// ignore exception thrown for double package load
			catch (\RuntimeException $e) {}
		}

		call_user_func($config);

		// When not set by the closure default to Kernel DiC
		( ! $this->dic instanceof DiC\Dependable) and $this->dic = new DiC\Base($this, _env('dic'));

		// Add the routes
		$this->router();
	}

	/**
	 * Define the routes for this application
	 */
	abstract public function router();

	/**
	 * Add a route to the Application
	 *
	 * @param   string        $name
	 * @param   string|array  $translation
	 * @return  \Fuel\Kernel\Route\Base
	 */
	public function add_route($name, $route)
	{
		if ($route instanceof Route\Base)
		{
			$this->routes[$name] = $route;
		}
		elseif (is_array($route))
		{
			array_unshift('Route', $route);
			$this->routes[$name] = call_user_func_array(array($this, 'forge'), $route);
		}
		else
		{
			$this->routes[$name] = $this->forge('Route', $name, $route);
		}
		return $this->routes[$name];
	}

	/**
	 * Add multiple routes
	 *
	 * @param   array  $routes
	 * @return  Base
	 */
	public function add_routes(array $routes)
	{
		foreach ($routes as $name => $route)
		{
			$this->add_route($name, $route);
		}
		return $this;
	}

	/**
	 * Allow for reverse routing
	 *
	 * @param   string  $name
	 * @return  \Fuel\Kernel\Route\Base
	 * @throws  \RuntimeException
	 */
	public function get_route($name)
	{
		if ( ! isset($this->routes[$name]))
		{
			throw new \RuntimeException('Requesting an unregistered route.');
		}
		return $this->routes[$name];
	}

	/**
	 * Attempts to route a given URI to a controller (class, Closure or callback)
	 *
	 * @param  string  $uri
	 */
	public function process_route($uri)
	{
		empty($uri) and $uri = '_root_';

		// Attempt other routes
		foreach ($this->routes as $route)
		{
			if ($route->matches($uri))
			{
				return $route->match();
			}
		}

		// If not found create a Fuel route
		$route = $this->forge('Route', $uri);
		if ($route->matches($uri))
		{
			return $route->match();
		}

		throw new Request\Exception_404($uri);
	}

	/**
	 * Create the application main request
	 *
	 * @param   string  $uri
	 * @return  \Fuel\Kernel\Request\Base
	 */
	public function request($uri)
	{
		$this->request = $this->forge('Request', $uri);
		return $this;
	}

	/**
	 * Execute the application main request
	 *
	 * @return  Base
	 */
	public function execute()
	{
		$this->activate();
		$this->request->execute();
		$this->deactivate();
		return $this;
	}

	/**
	 * Makes this Application the active one
	 *
	 * @return  Base  for method chaining
	 */
	public function activate()
	{
		$this->_before_activate = _env()->active_app();
		_env()->set_active_app($this);
		return $this;
	}

	/**
	 * Deactivates this Application and reactivates the previous active
	 *
	 * @return  Base  for method chaining
	 */
	public function deactivate()
	{
		_env()->set_active_app($this->_before_activate);
		$this->_before_activate = null;
		return $this;
	}

	/**
	 * Return the response object
	 *
	 * @return  \Fuel\Kernel\Response\Responsible
	 */
	public function response()
	{
		return $this->request->response();
	}

	/**
	 * Attempts to find one or more files in the packages
	 *
	 * @param   string  $location
	 * @param   string  $file
	 * @param   bool    $multiple
	 * @return  array|bool
	 */
	public function find_file($location, $file, $basepath = null, $multiple = false)
	{
		$return = $multiple ? array() : false;

		// First search app
		$path = $this->loader->find_file($location, $file, $basepath);
		if ($path)
		{
			if ( ! $multiple)
			{
				return $path;
			}
			$return[] = $path;
		}

		// If not found or searching for multiple continue with packages
		foreach ($this->packages as $pkg)
		{
			if ($path = _loader()->package($pkg)->find_file($location, $file, $basepath))
			{
				if ( ! $multiple)
				{
					return $path;
				}
				$return[] = $path;
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
	 * @param   $location
	 * @param   $file
	 * @return  array|bool
	 */
	public function find_files($location, $file, $basepath = null)
	{
		return $this->find_file($location, $file, $basepath, true);
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
			if ($found = _loader()->package($pkg)->find_controller($controller))
			{
				return $found;
			}
		}

		// all is lost
		return false;
	}

	/**
	 * Translates a classname to the one set in the DiC classes property
	 *
	 * @param   string  $class
	 * @return  string
	 */
	public function get_class($class)
	{
		return $this->dic->get_class($class);
	}

	/**
	 * Forges a new object for the given class, supporting DI replacement
	 *
	 * @param   string  $class
	 * @return  object
	 */
	public function forge($class)
	{
		return call_user_func_array(array($this->dic, 'forge'), func_get_args());
	}

	/**
	 * Fetch an instance from the DiC
	 *
	 * @param   string  $class
	 * @param   string  $name
	 * @return  object
	 * @throws  \RuntimeException
	 */
	protected function get_object($class, $name)
	{
		$this->get_object($class, $name);
	}

	/**
	 * Sets the current active request
	 *
	 * @param  \Fuel\Kernel\Request\Base  $request
	 */
	public function set_active_request($request)
	{
		$this->active_request = $request;
	}

	/**
	 * Returns current active Request
	 *
	 * @return  \Fuel\Kernel\Request\Base
	 */
	public function active_request()
	{
		return $this->active_request;
	}
}

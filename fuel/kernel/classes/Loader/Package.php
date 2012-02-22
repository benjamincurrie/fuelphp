<?php

namespace Fuel\Kernel\Loader;

class Package implements Loadable
{
	/**
	 * @var  string  basepath for the package
	 */
	protected $path = '';

	/**
	 * @var  string  base namespace for the package (with trailing backslash when not empty)
	 */
	protected $namespace = '';

	/**
	 * @var  array  package modules with array(relative path => relative subnamespace) (with trailing backslash)
	 */
	protected $modules = array();

	/**
	 * @var  array  registered classes, without the base namespace
	 */
	protected $classes = array();

	/**
	 * @var  bool|string  whether this package is routable (bool) or routability is triggered by a prefix (string)
	 */
	protected $routable = false;

	/**
	 * @var  string|\Closure  loader used, has build in 'psr' or 'fuelv1' and can be a closure
	 */
	protected $loader = 'psr';

	/**
	 * Attempt to load a class from the package
	 *
	 * @param   string  $class
	 * @return  bool
	 */
	public function load_class($class)
	{
		if (isset($this->classes[$class]))
		{
			require $this->classes[$class];
			return true;
		}

		if ($this->namespace and strpos($class, $this->namespace) !== 0)
		{
			return false;
		}
		$class = substr($class, strlen($this->namespace));

		$path = $this->path;
		foreach ($this->modules as $m_path => $m_namespace)
		{
			if (strpos($class, $m_namespace) === 0)
			{
				$class  = substr($class, strlen($m_namespace));
				$path  .= 'modules/'.$m_path.'/';
				break;
			}
		}
		$path .= 'classes/';

		switch ($this->loader)
		{
			case 'psr':
			case 'psr0':
				$path .= $this->psr_loader($class, $path);
				break;
			case 'fuelv1':
				$path .= $this->fuelv1_loader($class, $path);
				break;
			default:
				$path .= call_user_func_array($this->loader, array($class, $path));
		}

		if (is_file($path))
		{
			require $path;
			return true;
		}

		return false;
	}
	/**
	 * Converts a classname to a path using PSR-0 conventions
	 *
	 * NOTE: using the base namespace setting and usage of modules break PSR-0 convention. The paths are expected
	 * relative to the base namespace when used and optionally relative to the module's (sub)namespace.
	 *
	 * @param   string  $class
	 * @return  string
	 */
	protected function psr_loader($class)
	{
		$file  = '';
		if ($last_ns_pos = strripos($class, '\\'))
		{
			$namespace = substr($class, 0, $last_ns_pos);
			$class = substr($class, $last_ns_pos + 1);
			$file = str_replace('\\', '/', $namespace).'/';
		}
		$file .= str_replace('_', '/', $class).'.php';

		return $file;
	}

	/**
	 * Converts a classname to a path using Fuel v1.x convention, which means PSR-0 modified to lowercase.
	 *
	 * @param   string  $class
	 * @return  string
	 */
	protected function fuelv1_loader($class)
	{
		return strtolower($this->psr_loader($class));
	}

	/**
	 * Set a base path for the package
	 *
	 * @param   string  $path
	 * @return  Package
	 */
	public function set_path($path)
	{
		$this->path = rtrim($path, '/\\').'/';
		return $this;
	}

	/**
	 * Set a base namespace for the package, only classes from that namespace are loaded
	 *
	 * @param   string  $namespace
	 * @return  Package
	 */
	public function set_namespace($namespace)
	{
		$this->namespace = trim($namespace, '\\').'\\';
		return $this;
	}

	/**
	 * Add a module with path & namespace
	 *
	 * @param   string  $path
	 * @param   string  $namespace
	 * @return  Package
	 */
	public function add_module($path, $namespace)
	{
		$this->modules[trim($path, '/\\').'/'] = trim($namespace, '\\').'\\';
		return $this;
	}

	/**
	 * Remove a module from the package
	 *
	 * @param   string  $path
	 * @return  Package
	 */
	public function remove_module($path)
	{
		unset($this->modules[trim($path, '/\\').'/']);
		return $this;
	}

	/**
	 * Adds a class to the Package that doesn't need to be found
	 *
	 * @param   string  $class
	 * @param   string  $path
	 * @return  Package
	 */
	public function add_class($class, $path)
	{
		return $this->add_classes(array($class => $path));
	}

	/**
	 * Adds classes to the Package that don't need to be found
	 *
	 * @param   array  $classes
	 * @return  Package
	 */
	public function add_classes(array $classes)
	{
		foreach ($classes as $class => $path)
		{
			$this->classes[$class] = $path;
		}
		return $this;
	}

	/**
	 * Removes a class from the package
	 *
	 * @param   string  $class
	 * @return  Package
	 */
	public function remove_class($class)
	{
		unset($this->classes[$class]);
		return $this;
	}

	/**
	 * Change the type of class loader used, by default 'psr' and 'fuelv1' included
	 *
	 * @param   string  $loader
	 * @return  Package
	 * @throws  \RuntimeException
	 */
	public function set_loader($loader)
	{
		if ( ! method_exists($this, $loader.'_loader') and ! is_callable($loader))
		{
			throw new \RuntimeException('Loader type '.$loader.' not found in the Package instance.');
		}
		$this->loader = $loader;
		return $this;
	}

	/**
	 * Sets routability of this package
	 *
	 * @param   bool  $routable
	 * @return  Package
	 */
	public function set_routable($routable)
	{
		$this->routable = $routable;
		return $this;
	}

	/**
	 * Attempts to find a controller, loads the class and returns the classname if found
	 *
	 * @param   string  $controller
	 * @return  bool|string
	 */
	public function find_controller($controller)
	{
		// Exit if not routable
		if ( ! $this->routable)
		{
			return false;
		}
		elseif (is_string($this->routable))
		{
			// If string trigger isn't found at the beginning return false
			if (strpos(strtolower($controller), $this->routable.'/') !== 0)
			{
				return false;
			}
			// Strip trigger from controller name
			$controller = substr($controller, strlen($this->routable) + 1);
		}

		$namespace = $this->namespace;
		if ($pos = strpos($controller, '\\'))
		{
			$module = substr($controller, 0, $pos).'/';
			if (isset($this->modules[$module]))
			{
				$namespace  .= $this->modules[$module];
				$controller  = substr($controller, $pos + 1);
			}
		}

		$controller = $namespace.'Controller\\'.$controller;
		if ($this->load_class($controller))
		{
			return $this->namespace.$controller;
		}

		return false;
	}

	/**
	 * Attempts to find a specific file
	 *
	 * @param   string  $location
	 * @param   string  $file
	 * @param   string  $basepath
	 * @return  bool|string
	 */
	public function find_file($location, $file, $basepath = null)
	{
		$location  = trim($location, '/\\').'/';
		$basepath = is_null($basepath) ? 'resources/' : trim($basepath, '/\\').'/';

		// if given attempt specific module load
		if (($pos = strpos($file, ':')) !== false)
		{
			$module = substr($file, 0, $pos).'/';
			if (isset($this->modules[$module]))
			{
				if (is_file($path = $this->path.$module.$basepath.$location.substr($file, $pos + 1)))
				{
					return $path;
				}
			}
			return false;
		}

		// attempt fetch from base
		if (is_file($path = $this->path.$basepath.$location.$file))
		{
			return $path;
		}

		// attempt to find in modules
		foreach ($this->modules as $path => $ns)
		{
			if (is_file($path = $this->path.'modules/'.$path.$basepath.$location.$file))
			{
				return $path;
			}
		}

		// all is lost
		return false;
	}
}

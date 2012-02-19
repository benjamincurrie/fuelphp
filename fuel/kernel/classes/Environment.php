<?php

namespace Fuel\Kernel;

class Environment
{
	/**
	 * @constant  string  version identifier
	 */
	const VERSION = '2.0-alpha';

	/**
	 * @var  Environment  instance
	 */
	protected static $instance;

	/**
	 * Singleton may be evil but to allow multiple instances would be wrong
	 *
	 * @return  Environment
	 */
	public static function instance()
	{
		if (is_null(static::$instance))
		{
			static::$instance = new static;
		}

		return static::$instance;
	}

	/**
	 * @var  string  name of the current environment
	 */
	protected $name = 'development';

	/**
	 * @var  string|null  optional overwrite for system environment setting
	 */
	protected $locale = null;

	/**
	 * @var  string|null  timezone name for php.net/timezones
	 */
	protected $timezone = 'UTC';

	/**
	 * @var  bool  whether or not usage of MBSTRING extension is enabled
	 */
	protected $mbstring = true;

	/**
	 * @var  string|null  character encoding
	 */
	protected $encoding = 'UTF-8';

	/**
	 * @var  array  path to the packages directory
	 */
	protected $paths = array();

	/**
	 * @var  Loader  the loader container
	 */
	protected $loader;

	/**
	 * @var  array  container for environment variables
	 */
	protected $vars = array();

	/**
	 * @var  bool  whether or not classes from core are aliased to global
	 * @deprecated  this is for Fuel 1.x BC
	 */
	protected $global_core_alias = false;

	public function __construct()
	{
		$this->vars['init_time'] = microtime(true);
		$this->vars['init_mem']  = memory_get_usage();
	}

	/**
	 * Allows the overwriting of the environment settings, should only be run once
	 *
	 * @param   array  $config
	 * @return  Environment  to allow method chaining
	 */
	public function init(array $config)
	{
		static $init = false;

		if ($init)
		{
			trigger_error('Environment config shouldn\'t be initiated twice.', E_USER_NOTICE);
		}

		foreach ($config as $key => $val)
		{
			if (property_exists($this, $key))
			{
				$this->{$key} = $val;
			}
		}

		// When mbstring setting was not given default to availability
		if ( ! isset($config['mbstring']))
		{
			$this->mbstring = function_exists('mb_get_info');
		}

		// Actually set the locale, timezone and encoding
		$this->set_locale($this->locale);
		$this->set_timezone($this->timezone);
		$this->set_encoding($this->encoding);

		// Set the class & fileloader
		$this->set_loader($this->loader);

		$init = true;

		return $this;
	}

	/**
	 * Set the locale
	 *
	 * @param   string|null  $locale  locale name (OS dependent)
	 * @return  Environment  to allow method chaining
	 */
	public function set_locale($locale)
	{
		$this->locale = $locale;
		$this->locale and setlocale(LC_ALL, $this->locale);
		return $this;
	}

	/**
	 * Set the timezone
	 *
	 * @param   string|null  $timezone  timezone name (http://php.net/timezones)
	 * @return  Environment  to allow method chaining
	 */
	public function set_timezone($timezone)
	{
		$this->timezone = $timezone;
		$this->timezone and date_default_timezone_set($this->timezone);
		return $this;
	}

	/**
	 * Set the character encoding (only when mbstring is enabled)
	 *
	 * @param   string|null  $encoding  encoding name
	 * @return  Environment  to allow method chaining
	 */
	public function set_encoding($encoding)
	{
		$this->encoding = $encoding;
		$this->encoding and mb_internal_encoding($this->encoding);
		return $this;
	}

	/**
	 * Set the file & classloader
	 *
	 * @param   string|null|Loader  $loader  either a loader instance or its classname
	 * @return  Environment  to allow method chaining
	 */
	public function set_loader($loader)
	{
		// Get the loader from the given arg
		if (is_string($loader))
		{
			$loader = new $loader();
		}
		elseif (empty($loader))
		{
			require_once $this->path('fuel').'kernel/classes/Loader.php';
			use Fuel\Kernel\Loader;
			$loader = new Loader();
		}

		// Set the loader as a property and register it with PHP
		$this->loader = $loader;
		spl_autoload_register(array($this->loader, 'load_class'), true, true);

		// Add the Kernel as a core package
		$loader->load_package('kernel', Loader::TYPE_CORE);

		return $this;
	}

	/**
	 * Fetch the full path for a given pathname
	 *
	 * @param   string  $name
	 * @return  string
	 * @throws  \OutOfBoundsException
	 */
	public function path($name)
	{
		if ( ! isset($this->paths[$name]))
		{
			throw new \OutOfBoundsException('Unknown path requested.');
		}

		return $this->paths[$name];
	}

	/**
	 * Register a new named path
	 *
	 * @param   string  $name       name for the path
	 * @param   string  $path       the full path
	 * @param   bool    $overwrite  whether or not overwriting existing name is allowed
	 * @return  Kernel  to allow method chaining
	 * @throws  \OutOfBoundsException
	 */
	public function add_path($name, $path, $overwrite = false)
	{
		if ( ! $overwrite and isset($this->paths[$name]))
		{
			throw new \OutOfBoundsException('Already a path registered for name: '.$name);
		}

		$this->paths[$name] = $path;
		return $this;
	}

	/**
	 * Set a global variable
	 *
	 * @param   string  $name
	 * @param   mixed   $value
	 * @return  Environment  to allow method chaining
	 */
	public function set_var($name, $value)
	{
		$this->vars[$name] = $value;
		return $this;
	}

	/**
	 * Get a global variable
	 *
	 * @param   string  $name
	 * @param   mixed   $default  value to return when name is unknown
	 * @return  mixed
	 */
	public function get_var($name, $default = null)
	{
		if ( ! isset($this->vars[$name]))
		{
			return $default;
		}
		return $this->vars[$name];
	}

	/**
	 * Fetch the time that has elapsed since Fuel Kernel init
	 *
	 * @return  float
	 */
	public function time_elapsed()
	{
		return microtime(true) - $this->get_var('init_time');
	}

	/**
	 * Fetch the mem usage change since Fuel Kernel init
	 *
	 * @return  float
	 */
	public function mem_usage($peak = false)
	{
		$usage = $peak ? memory_get_peak_usage() : memory_get_usage();
		return $usage - $this->get_var('init_mem');
	}

	/**
	 * Make the protected variables publicly available
	 *
	 * @param   string  $name
	 * @return  mixed
	 * @throws  \OutOfBoundsException
	 */
	public function __get($name)
	{
		if ( ! property_exists($this, $name))
		{
			throw new \OutOfBoundsException('Fuel Environment has no such property.');
		}

		return $this->{$name};
	}
}

<?php

namespace Fuel\Kernel\Presenter;
use Fuel\Kernel\Application;

abstract class Base
{
	/**
	 * @var  string  path of the Viewable
	 */
	protected $_view_path = '';

	/**
	 * @var  \Fuel\Kernel\View\Viewable
	 */
	protected $_view;

	/**
	 * @var  \Fuel\Kernel\Application\Base
	 */
	protected $_app;

	/**
	 * @var  \Fuel\Kernel\Request\Base
	 */
	protected $_context;

	/**
	 * @var  string|null  method to be run upon the Presenter, nothing will be ran when null
	 */
	protected $_method = 'view';

	public function __construct()
	{
		$this->fetch_view();
		$this->before();
	}

	/**
	 * Magic Fuel method that is the setter for the current app
	 *
	 * @param  \Fuel\Kernel\Application\Base  $app
	 */
	public function _set_app(Application\Base $app)
	{
		$this->_app = $app;
		$this->_context = $app->active_request();
	}

	/**
	 * Fetches the Viewable based on the $view_name property or Presenter classname
	 */
	public function fetch_view()
	{
		if (empty($this->_view_path))
		{
			$class = get_class($this);
			if (($pos = strpos($class, 'Presenter\\')) !== false)
			{
				$class = substr($class, $pos + 10);
			}
			$this->_view_path = str_replace('\\', '/', strtolower($class));
		}

		$this->_view = $this->_app->forge('View', $this->_view_path);
	}

	/**
	 * Method to do general viewmodel setup
	 */
	public function before() {}

	/**
	 * Default method that'll be run upon the viewmodel
	 */
	abstract public function view();

	/**
	 * Method to do general viewmodel finishing up
	 */
	public function after() {}

	/**
	 * Uses magic setter on the Viewable
	 *
	 * @param  $name
	 * @param  $value
	 */
	public function __set($name, $value)
	{
		$this->_view->{$name} = $value;
	}

	/**
	 * Uses magic getter on the Viewable
	 *
	 * @param $name
	 * @return mixed
	 */
	public function __get($name)
	{
		return $this->_view->{$name};
	}

	protected function execute($method = null)
	{
		if ($method !== null)
		{
			$this->{$method}();
			$this->after();
		}
		elseif ( ! empty($this->_method))
		{
			$this->{$this->_method}();
			$this->_method = null;
			$this->after();
		}
	}

	/**
	 * Turns the presenter into a string
	 *
	 * @return  string
	 */
	public function __toString()
	{
		$this->_context->activate();
		$this->execute();
		$view = (string) $this->_view;
		$this->_context->deactivate();

		return $view;
	}
}

<?php

namespace Fuel\Kernel\Presenter;
use Fuel\Kernel\Application;
use Fuel\Kernel\View;

abstract class Base extends View\Base
{
	/**
	 * @var  \Fuel\Kernel\Loader\Loadable
	 */
	protected $_loader;

	/**
	 * @var  string|null  method to be run upon the Presenter, nothing will be ran when null
	 */
	protected $_method = 'view';

	public function __construct()
	{
		empty($this->_path) and $this->default_path();
		$this->before();
	}

	/**
	 * Magic Fuel method that is the setter for the current app
	 *
	 * @param  \Fuel\Kernel\Application\Base  $app
	 */
	public function _set_app(Application\Base $app)
	{
		parent::_set_app($app);
		$this->_loader = $app->loader;
	}

	/**
	 * Generates the View path based on the Presenter classname
	 *
	 * @return  Base
	 */
	public function default_path()
	{
		$class = get_class($this);
		if (($pos = strpos($class, 'Presenter\\')) !== false)
		{
			$class = substr($class, $pos + 10);
		}
		$this->_path = str_replace('\\', '/', strtolower($class));

		return $this;
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

<?php

namespace Fuel\Kernel\Presenter;

abstract class Base
{
	/**
	 * @var  string  path of the Viewable
	 */
	protected $view_path = '';

	/**
	 * @var  \Fuel\Kernel\View\Viewable
	 */
	protected $view;

	/**
	 * @var  \Fuel\Kernel\Application\Base
	 */
	protected $_app;

	public function __construct($app)
	{
		$this->_app = $app;
		$this->fetch_view();
	}

	/**
	 * Fetches the Viewable based on the $view_name property or Presenter classname
	 */
	public function fetch_view()
	{
		if (empty($this->view_path))
		{
			$class = get_class($this);
			if (($pos = strpos($class, 'Presenter\\')) !== false)
			{
				$class = substr($class, $pos + 10);
			}
			$this->view_path = str_replace('\\', '/', strtolower($class));
		}

		$this->view = $this->_app->forge('View', $this->view_name);
	}

	/**
	 * Uses magic setter on the Viewable
	 *
	 * @param  $name
	 * @param  $value
	 */
	public function __set($name, $value)
	{
		$this->view->{$name} = $value;
	}

	/**
	 * Uses magic getter on the Viewable
	 *
	 * @param $name
	 * @return mixed
	 */
	public function __get($name)
	{
		return $this->view->{$name};
	}

	/**
	 * Turns the presenter into a string
	 *
	 * @return  string
	 */
	public function __toString()
	{
		return (string) $this->view;
	}
}

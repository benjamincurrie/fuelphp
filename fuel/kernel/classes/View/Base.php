<?php

namespace Fuel\Kernel\View;
use Fuel\Kernel\Application;

class Base implements Viewable
{
	/**
	 * @var  array  data to be passed to the view
	 */
	protected $_data = array();

	/**
	 * @var  \Fuel\Kernel\Application\Base
	 */
	protected $_app;

	/**
	 * @var  \Fuel\Kernel\Request\Base
	 */
	protected $_context;

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
	 * Magic setter
	 *
	 * @param   string  $name
	 * @param   mixed   $value
	 * @throws  \LogicException
	 */
	public function __set($name, $value)
	{
		if (strlen($name) > 2 and $name[0] == '_' and $name[1] != '_')
		{
			throw new \LogicException('Properties with a single underscore prefix are preserved for Viewable usage.');
		}

		$this->_data[$name] = $value;
	}

	/**
	 * Magic getter
	 *
	 * @param   string  $name
	 * @return  mixed
	 * @throws  \OutOfBoundsException
	 */
	public function & __get($name)
	{
		if ( ! isset($this->_data[$name]))
		{
			throw new \OutOfBoundsException('Property "'.$name.'" not set upon Viewable.');
		}

		return $this->_data[$name];
	}

	/**
	 * Renders and returns the view output
	 *
	 * @return  string
	 */
	public function __toString()
	{
		return 'not yet implmented';
	}
}

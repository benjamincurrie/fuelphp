<?php

namespace Fuel\Kernel\View;
use Fuel\Kernel\Application;

class Base implements Viewable
{
	/**
	 * @var  null|string  path to the View file
	 */
	protected $_path;

	/**
	 * @var  null|string  raw template to use as View
	 */
	protected $_template;

	/**
	 * @var  array  data to be passed to the view
	 */
	protected $_data = array();

	/**
	 * @var  \Fuel\Kernel\Parser\Parsable
	 */
	protected $_parser;

	/**
	 * @var  \Fuel\Kernel\Application\Base
	 */
	protected $_app;

	/**
	 * @var  \Fuel\Kernel\Request\Base
	 */
	protected $_context;

	public function __construct($file = null, array $data = array())
	{
		$this->_path = $file;
		$this->_data = $data;
	}

	/**
	 * Magic Fuel method that is the setter for the current app
	 *
	 * @param  \Fuel\Kernel\Application\Base  $app
	 */
	public function _set_app(Application\Base $app)
	{
		$this->_app      = $app;
		$this->_context  = $app->active_request();
		$this->_parser   = $app->get_object('Parser');

		// Fetch the full path from the Application
		$this->_path and $this->set_filename($this->_path);
	}

	/**
	 * Change the View filename
	 *
	 * @param   string  $file
	 * @return  Base
	 */
	public function set_filename($file)
	{
		$this->_path      = $this->_app->find_file('views', $file.'.'.$this->_parser->extension());
		$this->_template  = null;
		return $this;
	}

	/**
	 * Change the View template string
	 *
	 * @param   string  $template
	 * @return  Base
	 */
	public function set_template($template)
	{
		$this->_path      = null;
		$this->_template  = $template;
		return $this;
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
	 * Render the View
	 *
	 * @return  string
	 */
	protected function render()
	{
		return $this->_path
			? $this->_parser->parse_file($this->_path, $this->_data)
			: $this->_parser->parse_string($this->_template, $this->_data);
	}

	/**
	 * Turns the presenter into a string
	 *
	 * @return  string
	 */
	public function __toString()
	{
		// First make sure the Request that created this is active
		$context_activated = false;
		if (_app()->active_request() !== $this->_context)
		{
			$this->_context->activate();
			$context_activated = true;
		}

		// Execute t
		$view = $this->render();

		// When a request was activated, deactivate now we're done
		$context_activated and $this->_context->deactivate();

		return $view;
	}
}

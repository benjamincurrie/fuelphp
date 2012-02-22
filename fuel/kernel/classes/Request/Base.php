<?php

namespace Fuel\Kernel\Request;
use Fuel\Kernel\Application;

abstract class Base
{
	/**
	 * @var  Base  request that created this one
	 */
	protected $parent;

	/**
	 * @var  array  requests that were created during this one
	 */
	protected $descendants = array();

	/**
	 * @var  Base  active request before activation of this one
	 */
	protected $_before_activate;

	/**
	 * @var  \Fuel\Kernel\Application\Base  app that created this request
	 */
	public $app;

	/**
	 * @var  \Fuel\Kernel\Response\Responsible  Response after execution
	 */
	public $response;

	/**
	 * Magic Fuel method that is the setter for the current app
	 *
	 * @param  \Fuel\Kernel\Application\Base  $app
	 */
	public function _set_app(Application\Base $app)
	{
		$this->app = $app;

		// Set request tree references
		$this->parent = $this->app->active_request();
		$this->parent and $this->parent->set_descendant($this);
	}

	/**
	 * Makes this Request the active one
	 *
	 * @return  Base  for method chaining
	 */
	public function activate()
	{
		$this->_before_activate = $this->app->active_request();
		$this->app->set_active_request($this);
		return $this;
	}

	/**
	 * Deactivates this Request and reactivates the previous active
	 *
	 * @return  Base  for method chaining
	 */
	public function deactivate()
	{
		$this->app->set_active_request($this->_before_activate);
		$this->_before_activate = null;
		return $this;
	}

	/**
	 * Returns the request that created this one
	 *
	 * @return  Base
	 */
	public function get_parent()
	{
		return $this->parent;
	}

	/**
	 * Adds a descendant to the current Request
	 *
	 * @param  Base  $request
	 */
	public function set_descendant(Base $request)
	{
		$this->descendants[] = $request;
	}

	/**
	 * Returns the array of requests created during this one
	 *
	 * @return  array
	 */
	public function get_descendants()
	{
		return $this->descendants;
	}

	/**
	 * Execute the request
	 *
	 * Must use $this->activate() as the first statement and $this->deactivate() as the last one
	 */
	abstract public function execute();

	/**
	 * Fetch the request response after execution
	 */
	public function response()
	{
		return $this->response;
	}
}

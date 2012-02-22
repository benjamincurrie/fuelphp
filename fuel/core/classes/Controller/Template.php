<?php

namespace Fuel\Core\Controller;
use Classes\Controller\Base;

class Template extends Base
{
	/**
	 * @var  string|\Fuel\Kernel\View\Viewable
	 */
	public $template;

	/**
	 * Extend the before() method to make the template Viewable available
	 * in the Controller actions
	 */
	public function before()
	{
		parent::before();
		$this->template = $this->app->forge('View', $this->template);
	}

	/**
	 * Extend the after() method to assign the template as a response body
	 * when nothing was returned by the action
	 */
	public function after($response)
	{
		empty($response) and $response = $this->template;
		return parent::after($response);
	}
}

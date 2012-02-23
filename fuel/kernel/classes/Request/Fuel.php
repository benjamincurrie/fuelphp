<?php

namespace Fuel\Kernel\Request;
use Fuel\Kernel\Application;

class Fuel extends \Classes\Request\Base
{
	/**
	 * @var  \Fuel\Kernel\Input
	 */
	protected $input;

	/**
	 * @var  string
	 */
	protected $request_uri = '';

	public function __construct($uri = '', array $input = array())
	{
		$this->request_uri  = (string) $uri;
		$this->input        = $input ?: _env('input');
	}

	public function _set_app(Application\Base $app)
	{
		parent::_set_app($app);

		// Create the new Input object when an array was passed
		if (is_array($this->input))
		{
			$this->input = $app->forge('Input',
				($this->parent and property_exists($this->parent, 'input'))
					? $this->parent->input
					: _env('input')
			);
		}
	}

	/**
	 * Execute the request
	 *
	 * Must use $this->activate() as the first statement and $this->deactivate() as the last one
	 *
	 * @return  Fuel
	 */
	public function execute()
	{
		$this->activate();

		$this->response = $this->app->forge('Response', 'Test');

		$this->deactivate();
		return $this;
	}
}

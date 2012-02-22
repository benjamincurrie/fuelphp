<?php

namespace Fuel\Kernel\Request;

class Fuel extends \Classes\Request\Base
{
	/**
	 * @var  string
	 */
	protected $request_uri = '';

	public function __construct($uri = '')
	{
		$this->request_uri = (string) $uri;
	}

	/**
	 * Execute the request
	 *
	 * Must use $this->activate() as the first statement and $this->deactivate() as the last one
	 */
	public function execute()
	{
		$this->activate();

		// $this->response =;

		$this->deactivate();
		return $this;
	}
}

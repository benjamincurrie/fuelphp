<?php

namespace Fuel\Oil\Controller;
use Classes;

abstract class Cli extends Classes\Controller\Base
{
	/**
	 * @var  \Fuel\Kernel\Cli
	 */
	protected $cli;

	/**
	 * Makes the CLI object available
	 */
	public function before()
	{
		$this->cli = $this->app->get_object('Cli');
	}

	public function after($response)
	{
		$response = parent::after($response);
		$response->body and $this->cli->write($response->body);

		return $response;
	}
}

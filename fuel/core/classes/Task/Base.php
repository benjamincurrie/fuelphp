<?php

namespace Fuel\Core\Task;
use Classes;

abstract class Base extends Classes\Controller\Base
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

	/**
	 * Puts any response on the command line
	 *
	 * @param   mixed  $response
	 * @return  \Fuel\Kernel\Response\Base
	 */
	public function after($response)
	{
		$response = parent::after($response);
		$response->body and $this->cli->write($response->body);

		return $response;
	}
}

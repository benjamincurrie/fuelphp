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
	 * Makes the CLI object available and adds command line items as args
	 */
	public function router(array $args)
	{
		$this->cli = $this->app->get_object('Cli');

		$i = 2;
		while (($arg = $this->cli->option($i)) and strncmp($arg, '-', 1) != 0)
		{
			array_push($args, $arg);
			$i++;
		}
		return parent::router($args);
	}

	public function after($response)
	{
		$response = parent::after($response);
		$response->body and $this->cli->write($response->body);

		return $response;
	}
}

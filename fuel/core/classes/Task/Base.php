<?php

namespace Fuel\Core\Task;
use Classes;

abstract class Base extends Classes\Controller\Base
{
	/**
	 * @var  \Fuel\Kernel\Cli
	 */
	protected $cli;

	public function after($response)
	{
		$response = parent::after($response);
		$response->body and $this->cli->write($response->body);

		return $response;
	}
}

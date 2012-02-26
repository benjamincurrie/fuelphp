<?php

namespace Fuel\Oil\Task;
use Classes;

class Composer extends Classes\Task\Base
{
	/**
	 * @var  string
	 */
	protected $command = '';

	public function before()
	{
		// CD to the root of Fuel and call up phpunit with a path to our config
		$this->command = 'php '._env()->path('fuel').'oil/resources/vendor/Composer/composer.phar ';
	}

	public function after($response)
	{
		chdir(_env()->path('fuel'));
		passthru($this->command);

		return parent::after($response);
	}

	/**
	 * Run the Composer install command
	 */
	public function action_install()
	{
		$this->command .= 'install';
	}

	/**
	 * Run the Composer update command
	 */
	public function action_update()
	{
		$this->command .= 'update';
	}
}

<?php

use Classes\Application;
use Classes\Route\Oil as Route;

class Oil extends Application\Base
{
	public function router()
	{
		$this->add_route('__commandline', new Route());
	}
}

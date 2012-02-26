<?php

use Classes\Application;
use Classes\Route\Fuel as Route;

class App extends Application\Base
{
	public function router()
	{
		$this->add_route('/', 'Welcome');

		$this->add_route('GET /(.*)', 'Welcome/catchall/$1');
	}
}

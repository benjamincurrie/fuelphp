<?php

use Classes\Application;
use Classes\Route;

class App extends Application\Base
{
	public function router()
	{
		$this->add_route('/', 'welcome');

		$this->add_route('(.*)', 'welcome/catchall/$1');
	}
}

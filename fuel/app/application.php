<?php

use Classes\Application;
use Classes\Route;

class App extends Application\Base
{
	public function router()
	{
		$this->add_route('_root_', 'welcome');
	}
}

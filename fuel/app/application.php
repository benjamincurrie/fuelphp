<?php

use Fuel\Kernel\Application;
use Fuel\Kernel\Router;

class App extends Application
{
	public function router(Router $router)
	{
		$router->add('/', 'welcome/index', 'homepage');
		// $router->add('GET /some/path', 'some/path/get');
		// $router->add('POST /some/path', 'some/path/post');
	}
}

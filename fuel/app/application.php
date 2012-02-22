<?php

use Classes\Application;
use Classes\Router;

class App extends Classes\Base
{
	public function router(Router $router)
	{
		$router->add('/', 'welcome/index', 'homepage');
		// $router->add('GET /some/path', 'some/path/get');
		// $router->add('POST /some/path', 'some/path/post');
	}
}

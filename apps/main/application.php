<?php

namespace Main;

use Fuel\Foundation\KernelApplication;
use Fuel\Routing\Router;

class Application extends KernelApplication
{

	public function router()
	{
		$router = new Router($this);

		$router->add('root', '/', '/welcome/index');
		$router->add('hello', '/hello/(:segment)', '/welcome/hello/$1');

		return $router;
	}

}

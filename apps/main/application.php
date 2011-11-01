<?php

namespace Main;

use Fuel\Foundation\KernelApplication;

class Application extends KernelApplication
{

	public function routes()
	{
		return array(
			'/' => 'Welcome::index',
		);
	}

}

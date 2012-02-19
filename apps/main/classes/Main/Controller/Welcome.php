<?php

namespace Main\Controller;

use Fuel\Http\Response;
use Fuel\Controller\Controller;

class Welcome extends Controller
{
	public function action_index()
	{
		return Response::make('Welcome!');
	}

	public function action_hello($name = 'world')
	{
		return Response::make('Hello, '.ucfirst($name).'!');
	}

}

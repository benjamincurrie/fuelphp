<?php

namespace Main\Controller;

use Fuel\Http\Response;

class Welcome
{
	public function action_index()
	{
		return Response::make('Welcome!');
	}
}

<?php

namespace Controller;
use Classes;

class Welcome extends Classes\Controller\Base
{
	public function action_index()
	{
		return '<p><strong>Homepage</strong></p>';
	}

	public function action_catchall()
	{
		$view = $this->app->forge('View', 'welcome');
		$view->body = '<p><strong>Catch-all: </strong>'.implode('/', func_get_args()).'</p>';
		return $view;
	}
}

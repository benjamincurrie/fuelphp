<?php

namespace Controller;
use Classes;

class Welcome extends Classes\Controller\Base
{
	public function action_index()
	{
		$view = $this->app->forge('View', 'welcome');
		$view->body = '<p><strong>TEST!</strong></p>';
		return $view;
	}

	public function action_catchall()
	{
		$view = $this->app->forge('View', 'welcome');
		$view->body = '<p><strong>Gevangen: </strong>'.implode('/', func_get_args()).'</p>';
		return $view;
	}
}

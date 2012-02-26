<?php

use Classes\Application;

class Oil extends Application\Base
{
	public function setup()
	{
		$this->dic->set_classes(array(
			'Route'  => 'Classes\\Route\\Oil',
			'View'   => 'Fuel\\Kernel\\View\\Base',
		));
	}

	public function router()
	{
		$this->add_route('__commandline', $this->forge('Route'));
	}
}

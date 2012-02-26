<?php

use Classes\Application;

class Oil extends Application\Base
{
	public function setup()
	{
		$this->dic->set_classes(array(
			'Route'  => 'Classes\\Route\\Task',
			'View'   => 'Fuel\\Kernel\\View\\Base',
		));
	}

	public function router()
	{
		$this->add_route('(\\-help)?', 'main/help');
		$this->add_route('\\-v(ersion)?', 'main/version');

		$this->add_route('c', 'console');
		$this->add_route('cell', 'cells');
		$this->add_route('g', 'generate');
		$this->add_route('r', 'refine');
		$this->add_route('t', 'test');
	}
}

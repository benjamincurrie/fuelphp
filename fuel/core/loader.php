<?php

use Fuel\Kernel\Loader;

return Loader::instance()->forge('Package')
	->set_path(__DIR__)
	->set_namespace('Fuel\\Core')
	->set_dic_classes(array(
		// 'Request' => 'Fuel\\Core\\Request',
	));
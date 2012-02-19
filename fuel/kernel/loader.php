<?php

__env()->add_path('kernel', __DIR__, true);

return __forge('Package')
	->set_path(__DIR__)
	->set_namespace('Fuel\\Kernel')
	->set_dic_classes(array(
		'Loader'   => 'Fuel\\Kernel\\Loader',
		'Package'  => 'Fuel\\Kernel\\Loader\\Package',
	));

<?php

// Add some paths to the global DiC
_env('dic')->set_classes(array(
	// 'Session' => 'Fuel\\Core\\Session',
));

// Add the frontclasses as global aliases to support Fuel v1 static usage
_loader()->add_global_ns_alias('Fuel\\Core\\Front');

// Forge and return the Core Package object
return _forge('Package')
	->set_path(__DIR__)
	->set_namespace('Fuel\\Core')
	->add_class_aliases(array(
		'Classes\\Controller\\Template'  => 'Fuel\\Core\\Controller\\Template',
		'Classes\\Request\\Curl'         => 'Fuel\\Core\\Request\\Curl',
	));

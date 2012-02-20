<?php

// Add some paths to the global DiC
_env()->dic->set_classes(array(
	// 'Request' => 'Fuel\\Core\\Request',
));

// Forge and return the Core Package object
return _forge('Package')
	->set_path(__DIR__)
	->set_namespace('Fuel\\Core');

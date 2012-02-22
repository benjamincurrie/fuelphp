<?php

// Add the Kernel path to the globally available paths
_env()->add_path('kernel', __DIR__, true);

// Add some Kernel classes to the global DiC
_env('dic')->set_classes(array(
	'Package'  => 'Fuel\\Kernel\\Loader\\Package',
	'Request'  => 'Fuel\\Kernel\\Request\\Fuel',
));

// Forge & return the Kernel Package object
return _forge('Package')
	->set_path(__DIR__)
	->set_namespace('Fuel\\Kernel');

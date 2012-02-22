<?php

// Add the Kernel path to the globally available paths
_env()->add_path('kernel', __DIR__, true);

// Add some Kernel classes to the global DiC
_env('dic')->set_classes(array(
	'Package'  => 'Fuel\\Kernel\\Loader\\Package',
	'Request'  => 'Fuel\\Kernel\\Request\\Fuel',
));

// Add aliases to allow extending into the classname
_loader()->add_class_aliases(array(
	'Classes\\Application\\Base'  => 'Fuel\\Kernel\\Application\\Base',

	'Classes\\Controller\\Base'  => 'Fuel\\Kernel\\Controller\\Base',

	'Classes\\Data\\Base'      => 'Fuel\\Kernel\\Data\\Base',
	'Classes\\Data\\Config'    => 'Fuel\\Kernel\\Data\\Config',
	'Classes\\Data\\Language'  => 'Fuel\\Kernel\\Data\\Language',

	'Classes\\DiC\\Base'  => 'Fuel\\Kernel\\DiC\\Base',

	'Classes\\Loader\\Base'  => 'Fuel\\Kernel\\Loader\\Base',

	'Classes\\Presenter\\Base'  => 'Fuel\\Kernel\\Presenter\\Base',

	'Classes\\Request\\Base'  => 'Fuel\\Kernel\\Request\\Base',
	'Classes\\Request\\Fuel'  => 'Fuel\\Kernel\\Request\\Fuel',

	'Classes\\Response\\Base'  => 'Fuel\\Kernel\\Response\\Base',

	'Classes\\View\\Base'  => 'Fuel\\Kernel\\View\\Base',
));

// Forge & return the Kernel Package object
return _forge('Package')
	->set_path(__DIR__)
	->set_namespace('Fuel\\Kernel');

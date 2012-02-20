<?php

/**
 * Here you setup your different environments
 * (put all defaults into '__default')
 */

return array(
	'__default' => function() {
		ini_set('display_errors', 'Off');

		return array(
			'locale'    => null,
			'language'  => 'en',
			'timezone'  => 'UTC',
			'encoding'  => 'UTF8',
		);
	},

	'development' => function() {
		error_reporting(-1);
	},

	'production' => function() {
		error_reporting(0);
	},

	'testing' => function() {
		error_reporting(-1);
		include_once 'PHPUnit/Autoload.php';
	},
);
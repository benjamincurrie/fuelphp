<?php

/**
 * Here you setup your different environments
 * (put all defaults into '__default')
 */

return array(
	'__default' => function() {
		// Switch off error display to allow Fuel to handle them
		// Uses suppression as some setups don't allow ini_set()
		//@ini_set('display_errors', 'Off');

		// Return array with environment config
		return array(
			'locale'    => null,
			'language'  => 'en',
			'timezone'  => 'UTC',
			'encoding'  => 'UTF-8',
			'packages'  => array('core'),
		);
	},

	'development' => function() {
		error_reporting(-1);
	},

	'production' => function() {
		error_reporting(0);
	},
);
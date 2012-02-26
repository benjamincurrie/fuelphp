<?php

_loader()->add_global_ns_alias('Fuel\\Legacy');

// Forge and return the Core Package object
return _forge('Package')
	->set_path(__DIR__)
	->set_namespace('Fuel\\Legacy');

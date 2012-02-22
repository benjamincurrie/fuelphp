<?php

_env()->register_app('app', 'App');

// Forge and return your Application Package object
return _forge('Package')
	->set_path(__DIR__);

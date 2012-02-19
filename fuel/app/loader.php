<?php

use Fuel\Kernel\Loader;

return Loader::instance()->forge('Package')
	->set_path(__DIR__);
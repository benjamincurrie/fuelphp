<?php

namespace Fuel\Core\Security\String;
use Fuel\Kernel\Security\String;
use htmLawed;

class Xss extends String\Base
{
	public static function clean($input)
	{
		if ( ! function_exists('htmLawed'))
		{
			require _env()->path('core').'vendor/htmlawed/htmLawed.php';
		}

		return htmLawed($input, array('safe' => 1, 'balanced' => 0));
	}
}

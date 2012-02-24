<?php

namespace Fuel\Kernel\Security;
use Fuel\Kernel\Application;

class Base
{
	/**
	 * @var  \Fuel\Kernel\Application\Base
	 */
	protected $app;

	/**
	 * @var  \Fuel\Kernel\Security\Csrf\Base
	 */
	public $csrf;

	/**
	 * @var  \Fuel\Kernel\Security\String\Base
	 */
	public $string;

	/**
	 * Magic Fuel method that is the setter for the current app
	 *
	 * @param  \Fuel\Kernel\Application\Base  $app
	 */
	public function _set_app(Application\Base $app)
	{
		$this->app = $app;

		$this->csrf    = $app->forge('Security_Csrf');
		$this->string  = $app->forge('Security_String');
	}

	/**
	 * Separate method for cleaning the URI
	 *
	 * @param   string  $uri
	 * @return  string
	 */
	public function clean_uri($uri)
	{
		return $this->clean($uri);
	}

	/**
	 * Clean a variable with the String cleaner
	 *
	 * @param   mixed  $input
	 * @return  mixed
	 */
	public function clean($input)
	{
		return $this->string->clean($input);
	}

	/**
	 * Fetch the CSRF token
	 *
	 * @return string
	 */
	public function get_token()
	{
		return $this->csrf->get_token();
	}

	/**
	 * Check the CSRF token
	 *
	 * @param   null|string  $token
	 * @return  bool
	 */
	public function check_token($token = null)
	{
		return $this->csrf->check_token($token);
	}
}

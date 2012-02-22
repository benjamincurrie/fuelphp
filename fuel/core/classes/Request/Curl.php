<?php

namespace Fuel\Core\Request;
use Classes\Request\Base;

class Curl extends Base
{
	/**
	 * @var  string  URL to serve as the resource for the CURL request
	 */
	protected $resource = '';

	/**
	 * @var  array  of HTTP request headers
	 */
	protected $headers = array();

	/**
	 * @var  string  HTTP request method
	 */
	protected $method = 'GET';

	/**
	 * @var  array  HTTP request method variables
	 */
	protected $request_vars = array();

	public function __construct($resource, array $options = array())
	{
		! is_array($resource) and $options['resource'] = $resource;

		foreach ($options as $var => $setting)
		{
			if (method_exists($this, $method = 'set_'.$var))
			{
				$this->{$method}($setting);
			}
			elseif (property_exists($this, $var))
			{
				$this->{$var} = $setting;
			}
		}
	}

	public function execute()
	{
		$this->activate();

		// $this->response = ;

		$this->deactivate();
		return $this;
	}
}

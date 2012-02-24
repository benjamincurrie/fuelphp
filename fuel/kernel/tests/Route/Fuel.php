<?php

namespace Fuel\Kernel\Route;

class FuelTest extends \PHPUnit_Framework_TestCase
{
	public function test_construct_full_params()
	{
		$uri = 'GET /route/test';
		$trans = '/re/routed';
		$methods = array('DELETE', 'PUT');
		$route = new Fuel($uri, $trans, $methods);
		$this->assertAttributeEquals($uri, 'search', $route);
		$this->assertAttributeEquals($trans, 'translation', $route);
		$this->assertAttributeEquals(array('DELETE', 'PUT', 'GET'), 'methods', $route);
	}

	public function test_construct_route_is_translation()
	{
		$uri = '/this/is/a/test';
		$route = new Fuel($uri);
		$this->assertAttributeEquals($uri, 'translation', $route);
	}

	public function test_construct_get_route()
	{
		$uri = '/this/is/a/test';
		$route = new Fuel('GET '.$uri);
		$this->assertAttributeEquals($uri, 'translation', $route);
		$this->assertAttributeEquals(array('GET'), 'methods', $route);
	}

	public function test_construct_put_post_route()
	{
		$uri = '/this/is/a/test';
		$route = new Fuel('PUT|POST '.$uri);
		$this->assertAttributeEquals($uri, 'translation', $route);
		$this->assertAttributeEquals(array('PUT', 'POST'), 'methods', $route);
	}
}

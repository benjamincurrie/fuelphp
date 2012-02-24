<?php

namespace Fuel\Kernel\Route;

class Fuel extends Base
{
	/**
	 * @var  array  HTTP methods
	 */
	protected $methods = array();

	/**
	 * @var  string  uri this must match
	 */
	protected $search = '';

	/**
	 * @var  string  uri it translates to
	 */
	protected $translation = '';

	/**
	 * @var  callback  something callable that matched
	 */
	protected $match;

	/**
	 * @var  array  URI segments
	 */
	protected $segments = array();

	public function __construct($search, $translation = null, array $methods = array())
	{
		$this->search   = $search;
		$this->methods  = $methods;

		// The search uri may start with allowed methods 'DELETE ' or multiple 'GET|POST|PUT '
		if (preg_match('#^(GET\\|?|POST\\|?|PUT\\|?|DELETE\\|?)+ #uD', $this->search, $matches))
		{
			$this->search   = ltrim(substr($this->search, strlen($matches[0])), ' /');
			$this->methods  = explode('|', $matches[1]);
		}

		$this->translation  = is_null($translation) ? $this->search : $translation;
	}

	/**
	 * Checks if the uri matches this route
	 *
	 * @param   string  $uri
	 * @return  bool    whether it matched
	 */
	public function matches($uri)
	{
		if ( ! empty($this->methods) and ! in_array(strtoupper($this->request->input()->method()), $this->methods))
		{
			return false;
		}

		if ($this->search instanceof \Closure)
		{
			// Given translation is superseded by the callback output when not just boolean true
			$translation = call_user_func($this->search, $uri, $this->app, $this->request);
			$translation === true and $translation = $this->translation;

			// When empty report failure
			if ( ! $translation)
			{
				return false;
			}
		}
		elseif ( ! ($translation = preg_replace('#^'.$this->search.'$#uD', $this->translation, $uri, -1, $count)))
		{
			return false;
		}

		return $this->parse($translation);
	}

	/**
	 * Attempts to find the controller and returns success
	 *
	 * @return  bool
	 */
	protected function parse($translation)
	{
		// Return directly if it's a Closure or a callable array
		if ($translation instanceof \Closure
			or (is_array($translation) and is_callable($translation)))
		{
			return true;
		}

		// Return Controller when found
		if (is_string($translation) and ($controller = $this->find_controller($translation)))
		{
			$this->match = array($this->app->forge($controller), 'router');
			return true;
		}

		// Failure...
		return false;
	}

	/**
	 * Parses the URI into a controller class
	 *
	 * @param  $uri
	 */
	protected function find_controller($uri)
	{
		$uri_array = explode('/', trim($uri, '/'));
		while ($uri_array)
		{
			if ($controller = $this->app->find_controller(implode('/', $uri_array)))
			{
				return $controller;
			}
			$this->segments[] = array_pop($uri_array);
		}
		return false;
	}

	/**
	 * Return an array with 1. callable to be the controller and 2. additional params array
	 *
	 * @return  array(callback, params)
	 */
	public function match()
	{
		return array($this->match, $this->segments);
	}
}

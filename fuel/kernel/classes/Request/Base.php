<?php

namespace Fuel\Kernel\Request;

interface Base
{
	/**
	 * Execute the request
	 */
	public function execute();

	/**
	 * Fetch the request response after execution
	 */
	public function response();
}

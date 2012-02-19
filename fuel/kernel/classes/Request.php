<?php

namespace Fuel\Kernel;

interface Request
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

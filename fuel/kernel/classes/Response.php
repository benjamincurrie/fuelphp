<?php

namespace Fuel\Kernel;

interface Response
{
	/**
	 * Send the response HTTP headers
	 */
	public function send_headers();

	/**
	 * Output the string response
	 */
	public function __toString();
}

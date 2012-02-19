<?php

namespace Fuel\Kernel\Response;

interface Base
{
	/**
	 * Constructor must take a body and an array of headers
	 *
	 * @param  mixed  $body
	 * @param  array  $headers
	 */
	public function __construct($body = '', array $headers = array());

	/**
	 * Must return the body of the response
	 *
	 * @return  string
	 */
	public function body();

	/**
	 * Send the response HTTP headers
	 */
	public function send_headers();

	/**
	 * Output the string response
	 *
	 * @return  string
	 */
	public function __toString();
}

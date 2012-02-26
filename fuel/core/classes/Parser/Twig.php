<?php

namespace Fuel\Core\Parser;
use Fuel\Kernel\Parser\Parsable;
use Twig_Environment;
use Twig_Loader_Filesystem;
use Twig_Loader_String;

class Twig implements Parsable
{
	/**
	 * @var  \Twig_Environment
	 */
	protected $parser;

	/**
	 * @var  \Twig_Loader_String
	 */
	protected $loader_string;

	/**
	 * Returns the expected file extension
	 *
	 * @return  string
	 */
	public function extension()
	{
		return 'twig';
	}

	/**
	 * Returns the Parser lib object
	 *
	 * @return  \Twig_Environment
	 */
	public function parser()
	{
		if ( ! empty($this->parser))
		{
			return $this->parser;
		}

		// Twig Environment
		$this->loader = new Twig_Loader_String();
		$this->parser = new Twig_Environment($this->loader);

		return $this->parser;
	}

	/**
	 * Parses a file using the given variables
	 *
	 * @param   string  $path
	 * @param   array   $data
	 * @return  string
	 */
	public function parse_file($path, array $data = array())
	{
		// Extract View name/extension (ex. "template.twig")
		$view_name = pathinfo($path, PATHINFO_BASENAME);

		// Use Twig filesystem loader
		$this->parser()->setLoader(new Twig_Loader_Filesystem(array(pathinfo($path, PATHINFO_DIRNAME))));

		try
		{
			return $this->parser()->loadTemplate($view_name)->render($data);
		}
		catch (\Exception $e)
		{
			// Delete the output buffer & re-throw the exception
			ob_end_clean();
			throw $e;
		}
	}

	/**
	 * Parses a given string using the given variables
	 *
	 * @param   string  $string
	 * @param   array   $data
	 * @return  string
	 */
	public function parse_string($template, array $data = array())
	{
		try
		{
			! $this->loader_string and $this->loader_string = new Twig_Loader_String();
			$this->parser()->setLoader($this->loader_string);
			return $this->parser()->loadTemplate($template)->render($data);
		}
		catch (\Exception $e)
		{
			// Delete the output buffer & re-throw the exception
			ob_end_clean();
			throw $e;
		}
	}
}

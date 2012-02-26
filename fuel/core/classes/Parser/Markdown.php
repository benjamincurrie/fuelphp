<?php

namespace Fuel\Core\Parser;
use Fuel\Kernel\Parser\Parsable;

class Markdown implements Parsable
{
	public function extension()
	{
		return 'md';
	}

	/**
	 * Returns the Parser lib object
	 *
	 * @return  \dflydev\markdown\MarkdownParser
	 */
	public static function parser()
	{
		static $parser = null;
		if (is_null($parser))
		{
			$parser = new \dflydev\markdown\MarkdownParser();
		}

		return $parser;
	}

	/**
	 * Parses a file using the given variables
	 *
	 * @param   string  $path
	 * @param   array   $data  @todo currently ignored by MD
	 * @return  string
	 */
	public function parse_file($path, array $data = array())
	{
		return $this->parse_string(file_get_contents($path), $data);
	}

	/**
	 * Parses a given string using the given variables
	 *
	 * @param   string  $string
	 * @param   array   $data  @todo  currently ignored by MD
	 * @return  string
	 */
	public function parse_string($string, array $data = array())
	{
		return $this->parser()->transform($string);
	}
}

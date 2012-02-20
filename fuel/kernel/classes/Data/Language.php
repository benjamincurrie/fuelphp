<?php

namespace Fuel\Kernel\Data;

class Language extends Base
{
	/**
	 * Load language file
	 *
	 * @param   string  $file
	 * @return  Config
	 */
	public function load($file, $language = null)
	{
		$language = $language ?: _env('language');
		$files = $this->_app->find_files('language/'.$language, $file);
		foreach ($files as $file)
		{
			$array = require $file;
			$this->_data = array_merge($this->_data, $array);
		}
		return $this;
	}
}
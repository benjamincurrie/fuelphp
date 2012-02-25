<?php

class helpersTest extends \PHPUnit_Framework_TestCase
{
	public function array_provider()
	{
		return array(
			array(
				'1st' => 'level',
				'first' => array(
					'2nd' => 'level',
					'second' => array(
						'3rd' => 'final'
					),
				),
			),
		);
	}

	/**
	 * @provider  array_provider
	 */
	public function test_array_set_dot_key($array)
	{
		$value = 'changed';

		array_set_dot_key('1st', $array, $value);
		$this->assertEquals($value, $array['1st']);

		array_set_dot_key('first.2nd', $array, $value);
		$this->assertEquals($value, $array['first']['2nd']);

		array_set_dot_key('first.second.3rd', $array, $value);
		$this->assertEquals($value, $array['first']['second']['3rd']);
	}

	/**
	 * @provider  array_provider
	 */
	public function test_array_get_dot_key($array)
	{
		array_get_dot_key('1st', $array, $return);
		$this->assertEquals('level', $return);

		array_set_dot_key('first.2nd', $array, $return);
		$this->assertEquals('level', $return);

		array_set_dot_key('first.second.3rd', $array, $return);
		$this->assertEquals('final', $return);
	}
}

<?php

/**
 * BakeOdd fixture
 *
 * @package LegacyFixture.Test.Fixture
 */
class BakeOddFixture extends CakeTestFixture {

/**
 * name property
 *
 * @var string
 */
	public $name = 'BakeOdd';

/**
 * useTable property
 *
 * @var string
 */
	public $table = 'bake_odd';

/**
 * field property
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true)
	);

/**
 * records property
 *
 * @var array
 */
	public $records = array(
		array('name' => 'Fred'),
		array('name' => 'Bob')
	);
}

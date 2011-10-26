<?php

use Orm\Events;

abstract class EventArguments_TestCase extends Events_TestCase
{
	protected $args = array(
		'id' => 123,
		'data' => array('foo' => 'bar'),
		'operation' => 'insert',
		'params' => array('foo' => true),
		'values' => array('foo' => 'bar'),
	);

	public static function dataProviderValuesTypes()
	{
		return array(
			array(Events::SERIALIZE_BEFORE),
			array(Events::SERIALIZE_AFTER),
			array(Events::SERIALIZE_CONVENTIONAL),
		);
	}
}

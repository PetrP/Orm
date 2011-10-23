<?php

abstract class EventArguments_TestCase extends TestCase
{
	protected $args = array(
		'id' => 123,
		'data' => array('foo' => 'bar'),
		'operation' => 'insert',
		'params' => array('foo' => true),
		'values' => array('foo' => 'bar'),
	);
}

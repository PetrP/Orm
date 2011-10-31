<?php

use Orm\RepositoryContainer;
use Orm\EventArguments;
use Orm\Events;

/**
 * @covers Orm\EventArguments::getArguments
 */
class EventArguments_getArguments_Test extends Events_TestCase
{
	private $r;
	protected function setUp()
	{
		$this->r = new TestsRepository(new RepositoryContainer);
	}

	/**
	 * @dataProvider dataProviderAll
	 */
	public function test($type)
	{
		$expectedArguments = array();
		if ($type & Events::PERSIST)
		{
			$expectedArguments = array('id' => 123);
		}
		else if ($type & (Events::HYDRATE_AFTER | Events::HYDRATE_BEFORE))
		{
			$expectedArguments = array('data' => array('id' => 123, 'string' => 'foo'));
		}
		else if ($type & (Events::SERIALIZE_AFTER | Events::SERIALIZE_CONVENTIONAL))
		{
			$expectedArguments = array('values' => array('id' => 123, 'string' => 'foo'), 'operation' => 'insert');
		}
		else if ($type & Events::SERIALIZE_BEFORE)
		{
			$expectedArguments = array('params' => array('id' => true, 'string' => true), 'values' => array('id' => 123, 'string' => 'foo'), 'operation' => 'insert');
		}
		$arguments = array(
			'id' => 123,
			'data' => array('id' => 123, 'string' => 'foo'),
			'operation' => 'insert',
			'params' => array('id' => true, 'string' => true),
			'values' => array('id' => 123, 'string' => 'foo'),
		);
		$args = new EventArguments($type, $this->r, new TestEntity, $arguments);
		$this->assertSame($expectedArguments, $args->getArguments());
	}

}

<?php

use Orm\RepositoryContainer;
use Orm\EventArguments;
use Orm\Events;

/**
 * @covers Orm\EventArguments::getArguments
 */
class EventArguments_getArguments_Test extends TestCase
{
	private $r;
	protected function setUp()
	{
		$this->r = new TestsRepository(new RepositoryContainer);
	}

	/**
	 * @dataProvider EventArguments_construct_Test::dataProviderTypes
	 */
	public function test($type)
	{
		$expectedArguments = array();
		if ($type & Events::PERSIST)
		{
			$expectedArguments = array('id' => 123);
		}
		else if ($type & (Events::LOAD_AFTER | Events::LOAD_BEFORE))
		{
			$expectedArguments = array('data' => array('id' => 123, 'string' => 'foo'));
		}
		$arguments = array('id' => 123, 'data' => array('id' => 123, 'string' => 'foo'));
		$args = new EventArguments($type, $this->r, new TestEntity, $arguments);
		$this->assertSame($expectedArguments, $args->getArguments());
	}

}

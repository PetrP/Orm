<?php

use Orm\RepositoryContainer;
use Orm\EventArguments;
use Orm\Events;

/**
 * @covers Orm\EventArguments::__construct
 * @covers Orm\EventArguments::getOperation
 */
class EventArguments_operation_Test extends EventArguments_TestCase
{
	private $r;
	protected function setUp()
	{
		$this->r = new TestsRepository(new RepositoryContainer);
	}

	/**
	 * @dataProvider dataProviderAll
	 */
	public function testHas($type)
	{
		if (!($type & (Events::SERIALIZE_BEFORE | Events::SERIALIZE_AFTER | Events::SERIALIZE_CONVENTIONAL)))
		{
			$this->setExpectedException('Orm\MemberAccessException', 'Cannot read an undeclared property Orm\EventArguments::$operation.');
		}
		$args = new EventArguments($type, $this->r, new TestEntity, $this->args);
		$this->assertSame('insert', $args->operation);
	}

	/**
	 * @dataProvider dataProviderAll
	 */
	public function testHasNot($type)
	{
		if ($type & (Events::SERIALIZE_BEFORE | Events::SERIALIZE_AFTER | Events::SERIALIZE_CONVENTIONAL))
		{
			$this->setExpectedException('Orm\InvalidArgumentException', "Orm\\EventArguments::\$operation must be update|insert; 'NULL' given.");
		}
		else
		{
			$this->assertTrue(true);
		}
		$args = $this->args;
		unset($args['operation']);
		new EventArguments($type, $this->r, new TestEntity, $args);
	}

	public function testWrite()
	{
		$args = new EventArguments(Events::SERIALIZE_BEFORE, $this->r, new TestEntity, $this->args);
		$this->setExpectedException('Orm\MemberAccessException', 'Cannot write to a read-only property Orm\EventArguments::$operation.');
		$args->operation = 'foo';
	}
}

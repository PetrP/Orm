<?php

use Orm\RepositoryContainer;
use Orm\EventArguments;
use Orm\Events;

/**
 * @covers Orm\EventArguments::__construct
 */
class EventArguments_type_Test extends EventArguments_TestCase
{
	private $r;
	protected function setUp()
	{
		$this->r = new TestsRepository(new RepositoryContainer);
	}

	/**
	 * @dataProvider dataProviderAll
	 */
	public function testType($type)
	{
		$args = new EventArguments($type, $this->r, new TestEntity, $this->args);
		$this->assertSame($type, $args->type);
	}

	public function testInvalid1()
	{
		$this->setExpectedException('Orm\InvalidArgumentException', "Orm\\EventArguments::\$type must be valid event type; 'foo' given.");
		new EventArguments('foo', $this->r, new TestEntity);
	}

	public function testInvalid2()
	{
		$this->setExpectedException('Orm\InvalidArgumentException', "Orm\\EventArguments::\$type must be valid event type; '2052' given.");
		new EventArguments(Events::REMOVE_AFTER | Events::ATTACH, $this->r, new TestEntity);
	}

	public function testWrite()
	{
		$args = new EventArguments(Events::ATTACH, $this->r, new TestEntity);
		$this->setExpectedException('Orm\MemberAccessException', 'Cannot write to a read-only property Orm\EventArguments::$type.');
		$args->type = 'foo';
	}
}

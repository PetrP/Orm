<?php

use Orm\RepositoryContainer;
use Orm\EventArguments;
use Orm\Events;

/**
 * @covers Orm\EventArguments::__construct
 * @covers Orm\EventArguments::getEntity
 */
class EventArguments_entity_Test extends EventArguments_TestCase
{
	private $r;
	protected function setUp()
	{
		$this->r = new TestsRepository(new RepositoryContainer);
	}


	/**
	 * @dataProvider dataProviderAll
	 */
	public function testHasEntity($type)
	{
		if ($type & (Events::FLUSH_BEFORE | Events::FLUSH_AFTER | Events::CLEAN_BEFORE | Events::CLEAN_AFTER))
		{
			$this->setExpectedException('Orm\MemberAccessException', 'Cannot read an undeclared property Orm\EventArguments::$entity.');
		}
		$e = new TestEntity;
		$args = new EventArguments($type, $this->r, $e, $this->args);
		$this->assertSame($e, $args->entity);
	}

	/**
	 * @dataProvider dataProviderAll
	 */
	public function testHasNotEntity($type)
	{
		if ($type & (Events::FLUSH_BEFORE | Events::FLUSH_AFTER | Events::CLEAN_BEFORE | Events::CLEAN_AFTER))
		{
			$this->assertTrue(true);
		}
		else
		{
			$this->setExpectedException('Orm\InvalidArgumentException', "Orm\\EventArguments::\$entity must be instance of Orm\\IEntity; 'NULL' given.");
		}
		new EventArguments($type, $this->r, NULL, $this->args);
	}

	public function testWrite()
	{
		$args = new EventArguments(Events::ATTACH, $this->r, new TestEntity);
		$this->setExpectedException('Orm\MemberAccessException', 'Cannot write to a read-only property Orm\EventArguments::$entity.');
		$args->entity = 'foo';
	}
}

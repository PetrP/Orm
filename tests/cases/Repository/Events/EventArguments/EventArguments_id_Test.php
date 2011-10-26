<?php

use Orm\RepositoryContainer;
use Orm\EventArguments;
use Orm\Events;

/**
 * @covers Orm\EventArguments::__construct
 */
class EventArguments_id_Test extends EventArguments_TestCase
{
	private $r;
	protected function setUp()
	{
		$this->r = new TestsRepository(new RepositoryContainer);
	}

	public function testPersist()
	{
		$args = new EventArguments(Events::PERSIST, $this->r, new TestEntity, array('id' => 123));
		$this->assertSame(123, $args->id);
		$this->assertSame(false, isset($args->data));
	}

	public function testPersistWrite()
	{
		$args = new EventArguments(Events::PERSIST, $this->r, new TestEntity, array('id' => 123));
		$this->assertSame(123, $args->id);
		$args->id = 598;
		$this->assertSame(598, $args->id);
	}

	public function testPersistNoId()
	{
		$this->setExpectedException('Orm\InvalidArgumentException', "Orm\\EventArguments::\$id must be scalar; 'NULL' given.");
		new EventArguments(Events::PERSIST, $this->r, new TestEntity);
	}

	public function testPersistIdNotScalar()
	{
		$this->setExpectedException('Orm\InvalidArgumentException', "Orm\\EventArguments::\$id must be scalar; 'array' given.");
		new EventArguments(Events::PERSIST, $this->r, new TestEntity, array('id' => array()));
	}

	/**
	 * @dataProvider dataProviderAll
	 */
	public function testReadId($type)
	{
		$args = new EventArguments($type, $this->r, new TestEntity, $this->args);
		if ($type === Events::PERSIST)
		{
			$this->assertTrue(true);
		}
		else
		{
			$this->setExpectedException('Orm\MemberAccessException', 'Cannot read an undeclared property Orm\EventArguments::$id.');
		}
		$args->id;
	}

	/**
	 * @dataProvider dataProviderAll
	 */
	public function testWriteId($type)
	{
		$args = new EventArguments($type, $this->r, new TestEntity, $this->args);
		if ($type === Events::PERSIST)
		{
			$this->assertTrue(true);
		}
		else
		{
			$this->setExpectedException('Orm\MemberAccessException', 'Cannot write to an undeclared property Orm\EventArguments::$id.');
		}
		$args->id = 123;
	}

}

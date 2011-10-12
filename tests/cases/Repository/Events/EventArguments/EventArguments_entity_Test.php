<?php

use Orm\RepositoryContainer;
use Orm\EventArguments;
use Orm\Events;

/**
 * @covers Orm\EventArguments::__construct
 */
class EventArguments_entity_Test extends TestCase
{
	private $r;
	protected function setUp()
	{
		$this->r = new TestsRepository(new RepositoryContainer);
	}


	/**
	 * @dataProvider EventArguments_construct_Test::dataProviderTypes
	 */
	public function testHasEntity($type)
	{
		$e = new TestEntity;
		$args = new EventArguments($type, $this->r, $e, array('id' => 123, 'data' => array('foo' => 'bar')));
		$this->assertSame($e, $args->entity);
	}

	/**
	 * @dataProvider EventArguments_construct_Test::dataProviderTypes
	 */
	public function testHasNotEntity($type)
	{
		$this->setExpectedException('Orm\InvalidArgumentException', "Orm\\EventArguments::\$entity must be instance of Orm\\IEntity; 'NULL' given.");
		new EventArguments($type, $this->r, NULL, array('id' => 123, 'data' => array('foo' => 'bar')));
	}

	public function testWrite()
	{
		$args = new EventArguments(Events::ATTACH, $this->r, new TestEntity);
		$this->setExpectedException('Orm\MemberAccessException', 'Cannot write to a read-only property Orm\EventArguments::$entity.');
		$args->entity = 'foo';
	}
}

<?php

use Orm\EntityWasRemovedException;
use Orm\RepositoryContainer;


/**
 * @covers Orm\EntityWasRemovedException
 */
class EntityWasRemovedException_Test extends TestCase
{

	public function test()
	{
		$e = new EntityWasRemovedException;
		$this->assertInstanceOf('LogicException', $e);
		$this->assertInstanceOf('Orm\EntityWasRemovedException', $e);
	}

	public function testMessage()
	{
		$e = new EntityWasRemovedException('foo, bar');
		$this->assertSame('foo, bar', $e->getMessage());
	}

	public function testMessageArray()
	{
		$e = new EntityWasRemovedException(array(new TestEntity));
		$this->assertSame('TestEntity was removed. Clone entity before reattach to repository.', $e->getMessage());

		$m = new RepositoryContainer;
		$e = new EntityWasRemovedException(array($m->tests->getById(2)));
		$this->assertSame('TestEntity#2 was removed. Clone entity before reattach to repository.', $e->getMessage());
	}
}

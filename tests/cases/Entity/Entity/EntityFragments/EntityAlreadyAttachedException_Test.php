<?php

use Orm\EntityAlreadyAttachedException;
use Orm\RepositoryContainer;

/**
 * @covers Orm\EntityAlreadyAttachedException
 */
class EntityAlreadyAttachedException_Test extends TestCase
{

	public function test()
	{
		$e = new EntityAlreadyAttachedException;
		$this->assertInstanceOf('LogicException', $e);
		$this->assertInstanceOf('Orm\EntityAlreadyAttachedException', $e);
	}

	public function testMessage()
	{
		$e = new EntityAlreadyAttachedException('foo, bar');
		$this->assertSame('foo, bar', $e->getMessage());
	}

	public function testMessageArray()
	{
		$e = new EntityAlreadyAttachedException(array(new TestEntity));
		$this->assertSame('TestEntity is already attached to another RepositoryContainer.', $e->getMessage());

		$m = new RepositoryContainer;
		$e = new EntityAlreadyAttachedException(array($m->tests->getById(2)));
		$this->assertSame('TestEntity#2 is already attached to another RepositoryContainer.', $e->getMessage());
	}
}

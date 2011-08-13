<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\Entity::getId
 */
class Entity_getId_Test extends TestCase
{
	private $r;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->r = $m->testentityrepository;
	}

	public function testPersisted()
	{
		$e = $this->r->getById(1);
		$this->assertSame(1, $e->id);
	}

	public function testUnpersisted()
	{
		$e = new TestEntity;
		$this->setExpectedException('Orm\EntityNotPersistedException', 'TestEntity is not persisted.');
		$e->id;
	}

	public function testReadOnly()
	{
		$e = new TestEntity;
		$this->setExpectedException('Orm\PropertyAccessException', 'Cannot write to a read-only property TestEntity::$id.');
		$e->id = 2;
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Entity', 'getId');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

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
		$this->setExpectedException('Nette\InvalidStateException', 'You must persist entity first');
		$e->id;
	}

	public function testReadOnly()
	{
		$e = new TestEntity;
		$this->setExpectedException('Nette\MemberAccessException', 'Cannot write to a read-only property TestEntity::$id.');
		$e->id = 2;
	}

}

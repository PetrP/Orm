<?php

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers Entity::getId
 */
class Entity_getId_Test extends TestCase
{
	private $r;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->r = $m->testentity;
	}

	public function testPersisted()
	{
		$e = $this->r->getById(1);
		$this->assertSame(1, $e->id);
	}

	public function testUnpersisted()
	{
		$e = new TestEntity;
		$this->setExpectedException('InvalidStateException', 'You must persist entity first');
		$e->id;
	}

	public function testReadOnly()
	{
		$e = new TestEntity;
		$this->setExpectedException('MemberAccessException', 'Cannot write to a read-only property TestEntity::$id.');
		$e->id = 2;
	}

}

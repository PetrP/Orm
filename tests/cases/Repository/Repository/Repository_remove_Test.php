<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\Repository::remove
 */
class Repository_remove_Test extends TestCase
{
	private $r;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->r = $m->Repository_remove_;
	}

	public function testNotPersist()
	{
		$e = new Repository_remove_Entity;
		$this->assertTrue($this->r->remove($e));
		$this->assertFalse(isset($e->id));
		$this->assertSame(0, $this->r->mapper->count);
	}

	public function testPersisted()
	{
		$e = $this->r->getById(2);
		$this->assertTrue($this->r->remove($e));
		$this->assertFalse(isset($e->id));
		$this->assertSame(NULL, $this->r->getById(2));
		$this->assertSame(1, $this->r->mapper->count);
	}

	public function testPersistedById()
	{
		$this->assertTrue($this->r->remove(2));
		$this->assertSame(NULL, $this->r->getById(2));
		$this->assertSame(1, $this->r->mapper->count);
	}

	public function testBadEntity()
	{
		$this->setExpectedException('Orm\InvalidEntityException', "Repository_remove_Repository can't work with entity 'Repository_persist_Entity', only with 'Repository_remove_Entity'");
		$this->r->remove(new Repository_persist_Entity);
	}

	public function testMapperError()
	{
		$this->r->mapper->returnNull = true;
		$this->setExpectedException('Orm\BadReturnException', "Repository_remove_Mapper::remove() must return TRUE; something wrong with mapper.");
		$this->r->remove($this->r->remove(2));
	}

	public function testRightEntityFromAnotherRepository()
	{
		$m = new RepositoryContainer;
		$e = $m->Repository_remove_->getById(1);
		$this->setExpectedException('Orm\InvalidEntityException', "Repository_remove_Entity#1 is attached to another repository.");
		$this->r->remove($e);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Repository', 'remove');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

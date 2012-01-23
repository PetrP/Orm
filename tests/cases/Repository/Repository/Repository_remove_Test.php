<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\Repository::remove
 */
class Repository_remove_Test extends TestCase
{
	private $r;

	public $events = array();

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->r = $m->Repository_remove_;

		$_this = $this;
		Repository_remove_Entity::$beforeRemove = function ($args) use ($_this) {
			$id = isset($args->entity->id) ? $args->entity->id : NULL;
			$_this->events[] = array($args->type, $id);
			if ($id)
			{
				$_this->assertSame($args->entity, $args->repository->getById($id));
				$_this->assertContains($args->entity, $_this->readAttribute($args->repository, 'entities'));
			}
		};
		Repository_remove_Entity::$afterRemove = function ($args) use ($_this) {
			list(, $id) = end($_this->events);
			$_this->events[] = array($args->type, isset($args->entity->id) ? $args->entity->id : NULL);
			if ($id)
			{
				$_this->assertSame(NULL, $args->repository->getById($id));
			}
			$_this->assertNotContains($args->entity, $_this->readAttribute($args->repository, 'entities'));
		};
	}

	public function testNotPersist()
	{
		$e = new Repository_remove_Entity;
		$this->assertTrue($this->r->remove($e));
		$this->assertFalse(isset($e->id));
		$this->assertSame(0, $this->r->mapper->count);
		$this->assertSame(array(), $this->events);
	}

	public function testPersistedNew()
	{
		$e = new Repository_remove_Entity;
		$this->r->persist($e);
		$this->assertTrue(isset($e->id));
		$this->assertSame($e, $this->r->getById(3));

		$this->assertTrue($this->r->remove($e));

		$this->assertFalse(isset($e->id));
		$this->assertSame(NULL, $this->r->getById(3));
		$this->assertSame(1, $this->r->mapper->count);
		$this->assertSame(array(
			array('beforeRemove', 3),
			array('afterRemove', 3),
		), $this->events);
	}

	public function testPersisted()
	{
		$e = $this->r->getById(2);
		$this->assertTrue($this->r->remove($e));
		$this->assertFalse(isset($e->id));
		$this->assertSame(NULL, $this->r->getById(2));
		$this->assertSame(1, $this->r->mapper->count);
		$this->assertSame(array(
			array('beforeRemove', 2),
			array('afterRemove', 2),
		), $this->events);
	}

	public function testPersistedById()
	{
		$this->assertTrue($this->r->remove(2));
		$this->assertSame(NULL, $this->r->getById(2));
		$this->assertSame(1, $this->r->mapper->count);
		$this->assertSame(array(
			array('beforeRemove', 2),
			array('afterRemove', 2),
		), $this->events);
	}

	public function testBadEntity()
	{
		$this->setExpectedException('UnexpectedValueException', "Repository_remove_Repository can't work with entity 'Repository_persist_Entity', only with 'Repository_remove_Entity'");
		try {
			$this->r->remove(new Repository_persist_Entity);
			$this->fail();
		} catch (Exception $e) {
			$this->assertSame(array(), $this->events);
			throw $e;
		}
	}

	public function testMapperError()
	{
		$this->r->mapper->returnNull = true;
		$this->setExpectedException('Nette\InvalidStateException', "Something wrong with mapper.");
		try {
			$this->r->remove($this->r->remove(2));
			$this->fail();
		} catch (Exception $e) {
			$this->assertSame(array(
				array('beforeRemove', 2),
			), $this->events);
			throw $e;
		}
	}

	public function testRightEntityFromAnotherRepository()
	{
		$m = new RepositoryContainer;
		$e = $m->Repository_remove_->getById(1);
		$this->setExpectedException('UnexpectedValueException', "Repository_remove_Entity#1 is attached to another repository.");
		try {
			$this->r->remove($e);
			$this->fail();
		} catch (Exception $e) {
			$this->assertSame(array(), $this->events);
			throw $e;
		}
	}
}

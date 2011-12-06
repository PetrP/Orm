<?php

use Orm\RepositoryContainer;
use Orm\ArrayCollection;

/**
 * @covers Orm\ArrayCollection::findBy
 */
class ArrayCollection_findBy_Test extends ArrayCollection_Base_Test
{

	public function testReturns()
	{
		$c = $this->c->findBy(array());
		$this->assertInstanceOf('Orm\ArrayCollection', $c);
		$this->assertSame('Orm\ArrayCollection', get_class($c));
		$this->assertNotSame($this->c, $c);
		$this->assertSame($this->e, $this->readAttribute($c, 'source'));
		$this->assertSame(NULL, $this->readAttribute($c, 'result'));
	}

	public function testReturnsSubClass()
	{
		$cOrigin = new ArrayCollection_ArrayCollection($this->e);
		$c = $cOrigin->findBy(array());
		$this->assertInstanceOf('Orm\ArrayCollection', $c);
		$this->assertSame('ArrayCollection_ArrayCollection', get_class($c));
		$this->assertNotSame($cOrigin, $c);
		$this->assertSame($this->e, $this->readAttribute($c, 'source'));
		$this->assertSame(NULL, $this->readAttribute($c, 'result'));
	}

	public function testBase()
	{
		$c = $this->c->findBy(array('string' => 'a'));
		$this->assertSame(array('a', 'a'), $c->fetchPairs(NULL, 'string'));
		$this->assertSame(array('a', 'b', 'a', 'b'), $this->c->fetchPairs(NULL, 'string'));
	}

	public function testEmpty()
	{
		$c = $this->c->findBy(array());
		$this->assertSame($this->e, $c->fetchAll());
	}

	public function testMore()
	{
		$c = $this->c->findBy(array('string' => 'a', 'int' => 3));
		$this->assertSame(array($this->e[2]), $c->fetchAll());
	}

	public function testUnexists()
	{
		$this->setExpectedException('Orm\MemberAccessException', 'Cannot read an undeclared property ArrayCollection_Entity::$unexists.');
		$this->c->findBy(array('unexists' => 'a'));
	}

	public function testIn()
	{
		$c = $this->c->findBy(array('int' => array(3, 4)));
		$this->assertSame(array($this->e[2], $this->e[3]), $c->fetchAll());
	}

	public function testEntity()
	{
		$model = new RepositoryContainer;
		$e = $model->tests->getById(1);
		$this->e[1]->e = $e;
		$c = $this->c->findBy(array('e' => $e));
		$this->assertSame(array($this->e[1]), $c->fetchAll());
	}

	public function testEntityId()
	{
		$model = new RepositoryContainer;
		$e = $model->tests->getById(1);
		$this->e[1]->e = $e;
		$c = $this->c->findBy(array('e' => 1));
		$this->assertSame(array($this->e[1]), $c->fetchAll());
	}

	public function testEntityNotPersist()
	{
		$e = new TestEntity;
		$c = $this->c->findBy(array('int' => $e));
		$this->assertSame(array(), $c->fetchAll());
	}

	public function testEntityNotPersist2()
	{
		$e = new TestEntity;
		$this->e[3]->e = $e;
		$c = $this->c->findBy(array('e' => $e));
		$this->assertSame(array($this->e[3]), $c->fetchAll());
	}

	public function testEntityNull()
	{
		$this->e[0]->e = new TestEntity;
		$this->e[1]->e = new TestEntity;
		$this->e[2]->e = new TestEntity;
		$c = $this->c->findBy(array('e' => NULL));
		$this->assertSame(array($this->e[3]), $c->fetchAll());
	}

	public function testEntityIn()
	{
		$model = new RepositoryContainer;
		$e1 = $model->tests->getById(1);
		$e2 = $model->tests->getById(2);
		$this->e[1]->e = $e1;
		$this->e[0]->e = $e2;
		$c = $this->c->findBy(array('e' => array($e1, $e2)));
		$this->assertSame(array($this->e[0], $this->e[1]), $c->fetchAll());
	}

	public function testEntityInForId()
	{
		$model = new RepositoryContainer;
		$this->e = array(
			$model->tests->getById(1),
			$model->tests->getById(2),
		);
		$this->c = new ArrayCollection($this->e);
		$c = $this->c->findBy(array('id' => array($this->e[1], $this->e[0])));
		$this->assertSame(array($this->e[0], $this->e[1]), $c->fetchAll());
		$c = $this->c->findBy(array('id' => array($this->e[1])));
		$this->assertSame(array($this->e[1]), $c->fetchAll());
	}

	public function testEntityInId()
	{
		$model = new RepositoryContainer;
		$e1 = $model->tests->getById(1);
		$e2 = $model->tests->getById(2);
		$this->e[1]->e = $e1;
		$this->e[0]->e = $e2;
		$c = $this->c->findBy(array('e' => array(1, 2)));
		$this->assertSame(array($this->e[0], $this->e[1]), $c->fetchAll());
	}

	public function testEntityInNotPersist()
	{
		$e1 = new TestEntity;
		$e2 = new TestEntity;
		$c = $this->c->findBy(array('e' => array($e1, $e2)));
		$this->assertSame(array(), $c->fetchAll());
	}

	public function testEntityInNotPersist2()
	{
		$e1 = new TestEntity;
		$this->e[3]->e = $e1;
		$c = $this->c->findBy(array('e' => array($e1)));
		$this->assertSame(array($this->e[3]), $c->fetchAll());
	}

	public function testEntityInNull()
	{
		$this->e[0]->e = new TestEntity;
		$this->e[1]->e = new TestEntity;
		$this->e[2]->e = $e = new TestEntity;
		$c = $this->c->findBy(array('e' => array(NULL, $e)));
		$this->assertSame(array($this->e[2], $this->e[3]), $c->fetchAll());
	}

	public function testEntityInByCollection()
	{
		$model = new RepositoryContainer;
		$e1 = $model->tests->getById(1);
		$e2 = $model->tests->getById(2);
		$this->e[1]->e = $e1;
		$this->e[0]->e = $e2;
		$c = $this->c->findBy(array('e' => new ArrayCollection(array($e1, $e2))));
		$this->assertSame(array($this->e[0], $this->e[1]), $c->fetchAll());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ArrayCollection', 'findBy');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

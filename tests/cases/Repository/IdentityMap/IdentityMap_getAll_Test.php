<?php

use Orm\RepositoryContainer;
use Orm\IdentityMap;

/**
 * @covers Orm\IdentityMap::getAll
 */
class IdentityMap_getAll_Test extends TestCase
{
	private $im;
	private $r;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->r = $m->tests;
		$this->im = new IdentityMap($this->r);
	}

	public function testAdd()
	{
		$e1 = new TestEntity;
		$e2 = new TestEntity;
		$this->im->add(1, $e1);
		$this->im->add(2, $e2);
		$this->assertSame(array(1 => $e1, 2 => $e2), $this->im->getAll());
	}

	public function testAttach()
	{
		$e1 = new TestEntity;
		$e2 = new TestEntity;
		$this->im->attach($e1);
		$this->im->add(2, $e2);
		$this->assertSame(array(2 => $e2), $this->im->getAll());
	}

	public function testAttachAndAdd()
	{
		$e1 = new TestEntity;
		$e2 = new TestEntity;
		$this->im->attach($e1);
		$this->im->add(1, $e1);
		$this->im->attach($e2);
		$this->im->add(2, $e2);
		$this->assertSame(array(1 => $e1, 2 => $e2), $this->im->getAll());
	}

	public function testChanged1()
	{
		$e1 = $this->r->getById(1);
		$e2 = new TestEntity;
		$this->im->add(1, $e1);
		$this->im->add(2, $e2);
		$this->assertSame(array(1 => $e1, 2 => $e2), $this->im->getAll());
	}

	public function testChanged2()
	{
		$e1 = $this->r->getById(1);
		$e2 = new TestEntity;
		$this->im->attach($e1);
		$this->im->attach($e2);
		$this->assertSame(array(), $this->im->getAll());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\IdentityMap', 'getAll');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

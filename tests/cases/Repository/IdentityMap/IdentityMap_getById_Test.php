<?php

use Orm\RepositoryContainer;
use Orm\IdentityMap;

/**
 * @covers Orm\IdentityMap::getById
 */
class IdentityMap_getById_Test extends TestCase
{
	private $im;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->im = new IdentityMap($m->tests);
	}

	public function testUnknown()
	{
		$this->assertSame(NULL, $this->im->getById(123));
		$this->assertSame(NULL, $this->im->getById(123));
	}

	public function testKnown()
	{
		$e = new TestEntity;
		$this->im->add(123, $e);
		$this->assertSame($e, $this->im->getById(123));
		$this->assertSame($e, $this->im->getById(123));
	}

	public function testRemoved()
	{
		$this->im->remove(123);
		$this->assertSame(false, $this->im->getById(123));
		$this->assertSame(false, $this->im->getById(123));
	}

	public function testAll()
	{
		$e = new TestEntity;
		$this->assertSame(NULL, $this->im->getById(123));
		$this->im->add(123, $e);
		$this->assertSame($e, $this->im->getById(123));
		$this->im->remove(123);
		$this->assertSame(false, $this->im->getById(123));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\IdentityMap', 'getById');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

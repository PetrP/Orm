<?php

use Orm\RepositoryContainer;
use Orm\IdentityMap;

/**
 * @covers Orm\IdentityMap::add
 */
class IdentityMap_add_Test extends TestCase
{
	private $im;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->im = new IdentityMap($m->tests);
	}

	public function test()
	{
		$this->assertSame(NULL, $this->im->getById(123));
		$e = new TestEntity;
		$this->assertSame(NULL, $this->im->add(123, $e));
		$this->assertSame($e, $this->im->getById(123));
		$this->assertSame(NULL, $this->im->add(123, $e));
		$e = new TestEntity;
		$this->assertSame(NULL, $this->im->add(123, $e));
		$this->assertSame($e, $this->im->getById(123));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\IdentityMap', 'add');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

<?php

use Orm\RepositoryContainer;
use Orm\IdentityMap;

/**
 * @covers Orm\IdentityMap::detach
 */
class IdentityMap_detach_Test extends TestCase
{
	private $im;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->im = new IdentityMap($m->tests);
	}

	public function test()
	{
		$e = new TestEntity;
		$this->assertSame(NULL, $this->im->attach($e));
		$this->assertSame(array($e), $this->im->getAllNew());
		$this->assertSame(NULL, $this->im->detach($e));
		$this->assertSame(array(), $this->im->getAllNew());
	}

	public function test2()
	{
		$e1 = new TestEntity;
		$e2 = new TestEntity;
		$this->assertSame(NULL, $this->im->attach($e1));
		$this->assertSame(NULL, $this->im->attach($e2));
		$this->assertSame(array($e1, $e2), $this->im->getAllNew());
		$this->assertSame(NULL, $this->im->detach($e1));
		$this->assertSame(array($e2), $this->im->getAllNew());
		$this->assertSame(NULL, $this->im->detach($e2));
		$this->assertSame(NULL, $this->im->detach($e2));
		$this->assertSame(array(), $this->im->getAllNew());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\IdentityMap', 'detach');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

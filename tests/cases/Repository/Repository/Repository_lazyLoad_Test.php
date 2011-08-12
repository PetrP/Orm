<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\Repository::lazyLoad
 */
class Repository_lazyLoad_Test extends TestCase
{
	private $r;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->r = $m->tests;
	}

	public function test()
	{
		$this->assertSame(array(), $this->r->lazyLoad(new TestEntity, 'xyz'));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Repository', 'lazyLoad');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

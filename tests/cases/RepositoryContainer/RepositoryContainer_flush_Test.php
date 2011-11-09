<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\RepositoryContainer::flush
 */
class RepositoryContainer_flush_Test extends TestCase
{
	private $m;

	protected function setUp()
	{
		$this->m = new RepositoryContainer;
	}

	public function test()
	{
		$r1 = $this->m->getRepository('RepositoryContainer_flush1');
		$r2 = $this->m->getRepository('RepositoryContainer_flush1');
		$this->assertSame(array(), $r1->count);
		$this->assertSame(array(), $r2->count);
		$this->m->flush();
		$this->assertSame(array('mapper'), $r1->count);
		$this->assertSame(array('mapper'), $r2->count);
		$this->m->flush();
		$this->assertSame(array('mapper', 'mapper'), $r1->count);
		$this->assertSame(array('mapper', 'mapper'), $r2->count);
	}

	public function testNoRepo()
	{
		$this->m->flush();
		$this->assertTrue(true);
	}

	public function testNotAttached()
	{
		$r = new RepositoryContainer_flush1Repository($this->m);
		$this->setExpectedException('Orm\RepositoryNotFoundException', "Repository 'RepositoryContainer_flush1Repository' is not attached to RepositoryContainer. It is impossible flush it. Do not inicialize your own repository, but ask RepositoryContainer for it.");
		$this->m->flush($r);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RepositoryContainer', 'flush');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

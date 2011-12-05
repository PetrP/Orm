<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\RepositoryContainer::clean
 * @covers Orm\RepositoryContainer::checkRepository
 */
class RepositoryContainer_clean_Test extends TestCase
{
	private $m;

	protected function setUp()
	{
		$this->m = new RepositoryContainer;
	}

	public function test()
	{
		$r1 = $this->m->getRepository('RepositoryContainer_clean1');
		$r2 = $this->m->getRepository('RepositoryContainer_clean1');
		$this->assertSame(array(), $r1->count);
		$this->assertSame(array(), $r2->count);
		$this->m->clean();
		$this->assertSame(array('mapper'), $r1->count);
		$this->assertSame(array('mapper'), $r2->count);
		$this->m->clean();
		$this->assertSame(array('mapper', 'mapper'), $r1->count);
		$this->assertSame(array('mapper', 'mapper'), $r2->count);
	}

	public function testNoRepo()
	{
		$this->m->clean();
		$this->assertTrue(true);
	}

	public function testNotAttached()
	{
		$r = new RepositoryContainer_clean1Repository($this->m);
		$this->setExpectedException('Orm\RepositoryNotFoundException', "Repository 'RepositoryContainer_clean1Repository' is not attached to RepositoryContainer. It is impossible clean it. Do not inicialize your own repository, but ask RepositoryContainer for it.");
		$this->m->clean($r);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RepositoryContainer', 'clean');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

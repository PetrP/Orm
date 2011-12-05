<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\RepositoryContainer::flush
 * @covers Orm\RepositoryContainer::checkRepository
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

	public function testPersistAll()
	{
		$r = $this->m->tests;
		$e1 = $r->attach(new TestEntity);
		$e2 = $r->attach(new TestEntity);
		$this->assertSame(true, $e1->isChanged());
		$this->assertSame(true, $e2->isChanged());
		$this->assertSame(false, isset($e1->id));
		$this->assertSame(false, isset($e2->id));
		$this->m->flush();
		$this->assertSame(false, $e1->isChanged());
		$this->assertSame(false, $e2->isChanged());
		$this->assertSame(true, isset($e1->id));
		$this->assertSame(true, isset($e2->id));
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

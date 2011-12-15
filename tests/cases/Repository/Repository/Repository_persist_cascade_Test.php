<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\Repository::persist
 * @covers Orm\Repository::getFkForEntity
 */
class Repository_persist_cascade_Test extends TestCase
{
	private $r1;
	private $r2;
	private $r3;
	private $r4;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->r1 = $m->Repository_persist_cascade1_;
		$this->r2 = $m->Repository_persist_cascade2_;
		$this->r3 = $m->Repository_persist_cascade3_;
		$this->r4 = $m->Repository_persist_cascade4_;
	}

	public function testM1()
	{
		$e = new Repository_persist_cascade1_Entity;
		$e->m1 = new Repository_persist_cascade2_Entity;
		$this->r1->persist($e);
		$this->assertSame(array(3), $this->r1->mapper->dump);
	}

	public function test1M()
	{
		$e = new Repository_persist_cascade2_Entity;
		$this->r2->attach($e);
		$e->{'1m'}->add(new Repository_persist_cascade1_Entity);
		$e->{'1m'}->add(new Repository_persist_cascade1_Entity);
		$this->r2->persist($e);
		$this->assertSame(array(3, 3), $this->r1->mapper->dump);
	}

	public function testMMLeft()
	{
		$e = new Repository_persist_cascade3_Entity;
		$this->r3->attach($e);
		$e->mm->add(new Repository_persist_cascade4_Entity);
		$e->mm->add(new Repository_persist_cascade4_Entity);
		$this->r3->persist($e);
		$this->assertSame(array(array(), array(3 => 3, 4 => 4)), $this->r3->mapper->dump);
		$this->assertSame(array(NULL, NULL), $this->r4->mapper->dump);
		$this->assertSame(array(
			array(array('add', 3, array(3 => 3, 4 => 4))),
			NULL,
		), $this->r3->mapper->dumpMany);
	}

	public function testMMRight()
	{
		$e = new Repository_persist_cascade4_Entity;
		$this->r4->attach($e);
		$e->mm->add(new Repository_persist_cascade3_Entity);
		$e->mm->add(new Repository_persist_cascade3_Entity);
		$this->r4->persist($e);
		$this->assertSame(array(array(),  array(3 => 3), array(), array(3 => 3), ), $this->r3->mapper->dump);
		$this->assertSame(array(NULL), $this->r4->mapper->dump);
		$this->assertSame(array(
			NULL,
			array(
				array('add', 3, array(3 => 3)),
				array('add', 4, array(3 => 3)),
			),
		), $this->r3->mapper->dumpMany);
	}

	private function mmBoth()
	{
		$e = new Repository_persist_cascade3_Entity;
		$e1 = new Repository_persist_cascade4_Entity;
		$e2 = new Repository_persist_cascade4_Entity;
		$this->r3->attach($e);
		$this->r4->attach($e1);
		$this->r4->attach($e2);
		$e1->mm->add($e);
		$e2->mm->add($e);
		$e->mm->add($e1);
		$e->mm->add($e2);
		return array($e, $e1, $e2);
	}

	public function testMMBoth1()
	{
		list($e, $e1, $e2) = $this->mmBoth();
		$this->r3->persist($e);
		$this->assertSame(array(array(), array(3 => 3, 4 => 4)), $this->r3->mapper->dump);
		$this->assertSame(array(NULL, NULL), $this->r4->mapper->dump);
		$this->assertSame(array(
			array(array('add', 3, array(3 => 3, 4 => 4))),
			NULL,
		), $this->r3->mapper->dumpMany);
	}

	public function testMMBoth2()
	{
		list($e, $e1, $e2) = $this->mmBoth();
		$this->r4->persist($e1);
		$this->assertSame(array(array(), array(3 => 3, 4 => 4)), $this->r3->mapper->dump);
		$this->assertSame(array(NULL, NULL), $this->r4->mapper->dump);
		$this->assertSame(array(
			NULL,
			array(array('add', 3, array(3 => 3, 4 => 4))),
		), $this->r3->mapper->dumpMany);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Repository', 'persist');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

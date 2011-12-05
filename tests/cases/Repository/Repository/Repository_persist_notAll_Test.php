<?php

use Orm\RepositoryContainer;
use Orm\Events;
use Orm\EventArguments;

/**
 * @covers Orm\Repository::persist
 */
class Repository_persist_notAll_Test extends TestCase
{
	private $r;
	private $r1;
	private $r2;
	private $r3;
	private $r4;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->r = $m->tests;
		$this->r1 = $m->Repository_persist_cascade1_;
		$this->r2 = $m->Repository_persist_cascade2_;
		$this->r3 = $m->Repository_persist_cascade3_;
		$this->r4 = $m->Repository_persist_cascade4_;
	}

	public function testBase1()
	{
		$e = new TestEntity;
		$this->r->persist($e, false);
		$this->assertSame(true, isset($e->id));
		$this->assertSame(false, $e->isChanged());
	}

	public function testBase2()
	{
		$e = $this->r->getById(1);
		$e->markAsChanged();
		$this->r->persist($e, false);
		$this->assertSame(true, isset($e->id));
		$this->assertSame(false, $e->isChanged());
	}

	public function testToOne1()
	{
		$e = new Repository_persist_cascade1_Entity;
		$e2 = new Repository_persist_cascade2_Entity;
		$e->markAsChanged();
		$e2->markAsChanged();
		$e->m1 = $e2;
		$this->r1->persist($e, false);
		$this->assertSame(true, isset($e->id));
		$this->assertSame(false, $e->isChanged());
		$this->assertSame(true, isset($e->id));
		$this->assertSame(false, $e->isChanged());
	}

	public function testToOne2()
	{
		$e = new Repository_persist_cascade1_Entity;
		$e2 = $this->r2->getById(1);
		$e->markAsChanged();
		$e2->markAsChanged();
		$e->m1 = $e2;
		$this->r1->persist($e, false);
		$this->assertSame(true, isset($e->id));
		$this->assertSame(false, $e->isChanged());
		$this->assertSame(true, isset($e2->id));
		$this->assertSame(true, $e2->isChanged());
	}

	public function testOneToMany1()
	{
		$e = $this->r1->getById(1);
		$e2 = new Repository_persist_cascade2_Entity;
		$e->markAsChanged();
		$e2->markAsChanged();
		$e2->{'1m'}->add($e);
		$this->r2->persist($e2, false);
		$this->assertSame(true, isset($e->id));
		$this->assertSame(true, $e->isChanged());
		$this->assertSame(true, isset($e2->id));
		$this->assertSame(false, $e2->isChanged());
	}

	public function testOneToMany2()
	{
		$e = $this->r1->getById(1);
		$e2 = $this->r2->getById(1);
		$e->markAsChanged();
		$e2->markAsChanged();
		$e2->{'1m'}->add($e);
		$this->r2->persist($e2, false);
		$this->assertSame(true, isset($e->id));
		$this->assertSame(true, $e->isChanged());
		$this->assertSame(true, isset($e2->id));
		$this->assertSame(false, $e2->isChanged());
	}

	public function testOneToMany3()
	{
		$e = new Repository_persist_cascade1_Entity;
		$e2 = $this->r2->getById(1);
		$e->markAsChanged();
		$e2->markAsChanged();
		$e2->{'1m'}->add($e);
		$this->r2->persist($e2, false);
		$this->assertSame(true, isset($e->id));
		$this->assertSame(false, $e->isChanged());
		$this->assertSame(true, isset($e2->id));
		$this->assertSame(false, $e2->isChanged());
	}

	public function testOneToMany4()
	{
		$e = $this->r1->attach(new Repository_persist_cascade1_Entity);
		$e2 = new Repository_persist_cascade2_Entity;
		$e->markAsChanged();
		$e2->markAsChanged();
		$e2->{'1m'}->add($e);
		$this->r2->persist($e2, false);
		$this->assertSame(true, isset($e->id));
		$this->assertSame(false, $e->isChanged());
		$this->assertSame(true, isset($e2->id));
		$this->assertSame(false, $e2->isChanged());
	}

	public function testManyToMany1()
	{
		$e = $this->r3->getById(1);
		$e2 = new Repository_persist_cascade4_Entity;
		$e->markAsChanged();
		$e2->markAsChanged();
		$e2->mm->add($e);
		$this->r4->persist($e2, false);
		$this->assertSame(true, isset($e->id));
		$this->assertSame(true, $e->isChanged());
		$this->assertSame(true, isset($e2->id));
		$this->assertSame(false, $e2->isChanged());
	}

	public function testManyToMany2()
	{
		$e = $this->r3->getById(1);
		$e2 = $this->r4->getById(1);
		$e->markAsChanged();
		$e2->markAsChanged();
		$e2->mm->add($e);
		$this->r4->persist($e2, false);
		$this->assertSame(true, isset($e->id));
		$this->assertSame(true, $e->isChanged());
		$this->assertSame(true, isset($e2->id));
		$this->assertSame(false, $e2->isChanged());
	}

	public function testManyToMany3()
	{
		$e = new Repository_persist_cascade3_Entity;
		$e2 = $this->r4->getById(1);
		$e->markAsChanged();
		$e2->markAsChanged();
		$e2->mm->add($e);
		$this->r4->persist($e2, false);
		$this->assertSame(true, isset($e->id));
		$this->assertSame(false, $e->isChanged());
		$this->assertSame(true, isset($e2->id));
		$this->assertSame(false, $e2->isChanged());
	}

	public function testManyToMany4()
	{
		$e = $this->r3->attach(new Repository_persist_cascade3_Entity);
		$e2 = new Repository_persist_cascade4_Entity;
		$e->markAsChanged();
		$e2->markAsChanged();
		$e2->mm->add($e);
		$this->r4->persist($e2, false);
		$this->assertSame(true, isset($e->id));
		$this->assertSame(false, $e->isChanged());
		$this->assertSame(true, isset($e2->id));
		$this->assertSame(false, $e2->isChanged());
	}

}

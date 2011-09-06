<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\Repository::persist
 */
class Repository_persist_recursion_Test extends TestCase
{
	private $r1;
	private $r2;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->r1 = $m->Repository_persist_recursion1_;
		$this->r2 = $m->Repository_persist_recursion2_;
	}

	public function testM1()
	{
		$e = new Repository_persist_recursion1_Entity;
		$e->m1 = $e;
		$this->setExpectedException('Nette\InvalidStateException', 'There is an infinite recursion during persist in Repository_persist_recursion1_Entity');
		$this->r1->persist($e);
	}

	public function test1M()
	{
		$e = new Repository_persist_recursion1_Entity;
		$this->r1->attach($e);
		$e->{'1m'}->add($e);
		$this->setExpectedException('Nette\InvalidStateException', 'There is an infinite recursion during persist in Repository_persist_recursion1_Entity');
		$this->r1->persist($e);
	}

	public function testOverMore()
	{
		$e1 = new Repository_persist_recursion1_Entity;
		$e2 = new Repository_persist_recursion1_Entity;
		$e3 = new Repository_persist_recursion1_Entity;
		$e1->m1 = $e2;
		$e2->m1 = $e3;
		$e3->m1 = $e1;
		$this->setExpectedException('Nette\InvalidStateException', 'There is an infinite recursion during persist in Repository_persist_recursion1_Entity');
		$this->r1->persist($e1);
	}

	public function testMM()
	{
		$e = new Repository_persist_recursion2_Entity;
		$this->r2->attach($e);
		$e->mma->add($e);
		$e->mmb->add($e);
		$this->setExpectedException('Nette\NotSupportedException', 'Orm\ArrayManyToManyMapper has support only on side where is realtionship mapped.');
		$this->r2->persist($e);
		$this->assertTrue(true); // mm se nezacykly
	}

}

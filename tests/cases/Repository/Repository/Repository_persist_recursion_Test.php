<?php

use Orm\RepositoryContainer;
use Orm\Events;
use Orm\EventArguments;

/**
 * @covers Orm\Repository::persist
 */
class Repository_persist_recursion_Test extends TestCase
{
	private $m;
	private $r1;
	private $r2;

	protected function setUp()
	{
		$this->m = new RepositoryContainer;
		$this->r1 = $this->m->Repository_persist_recursion1_;
		$this->r2 = $this->m->Repository_persist_recursion2_;
	}

	public function testM1()
	{
		$e = new Repository_persist_recursion1_Entity;
		$e->m1 = $e;
		$this->setExpectedException('Orm\RecursiveException', 'There is an infinite recursion during persist in Repository_persist_recursion1_Entity', 2);
		$this->r1->persist($e);
	}

	public function test1M()
	{
		$e = new Repository_persist_recursion1_Entity;
		$this->r1->attach($e);
		$e->{'1m'}->add($e);
		$this->setExpectedException('Orm\RecursiveException', 'There is an infinite recursion during persist in Repository_persist_recursion1_Entity', 2);
		$this->r1->persist($e);
	}

	public function test11()
	{
		$e1 = new Repository_persist_recursion3_Entity;
		$e2 = new Repository_persist_recursion3_Entity;
		$e1->{'11'} = $e2;
		$e2->{'11'} = $e1;
		$this->setExpectedException('Orm\RecursiveException', 'There is an infinite recursion during persist in Repository_persist_recursion3_Entity', 2);
		$this->m->Repository_persist_recursion3_->persist($e1);
	}

	public function test11Linked()
	{
		$e1 = new Repository_persist_recursion_linked3_Entity;
		$e2 = new Repository_persist_recursion_linked3_Entity;
		$e1->{'11'} = $e2;
		$this->setExpectedException('Orm\RecursiveException', 'There is an infinite recursion during persist in Repository_persist_recursion_linked3_Entity', 2);
		$this->m->Repository_persist_recursion_linked3_->persist($e1);
	}

	private $c = array();
	public function persistCount(EventArguments $args)
	{
		$this->c[] = get_class($args->repository);
	}

	public function test11WithNull1()
	{
		$this->m->Repository_persist_recursion4_->events->addCallbackListener(Events::PERSIST, array($this, 'persistCount'));
		$this->m->Repository_persist_recursion5_->events->addCallbackListener(Events::PERSIST, array($this, 'persistCount'));
		$e1 = new Repository_persist_recursion4_Entity;
		$e2 = new Repository_persist_recursion5_Entity;
		$e1->{'11'} = $e2;
		$e2->{'11'} = $e1;
		$this->m->Repository_persist_recursion4_->persist($e1);
		$this->assertSame(array(
			'Repository_persist_recursion4_Repository',
			'Repository_persist_recursion5_Repository',
			'Repository_persist_recursion4_Repository',
		), $this->c);
		$this->assertFalse($e1->isChanged());
		$this->assertFalse($e2->isChanged());
	}

	public function test11LinkedWithNull1()
	{
		$this->m->Repository_persist_recursion_linked4_->events->addCallbackListener(Events::PERSIST, array($this, 'persistCount'));
		$this->m->Repository_persist_recursion_linked5_->events->addCallbackListener(Events::PERSIST, array($this, 'persistCount'));
		$e1 = new Repository_persist_recursion_linked4_Entity;
		$e2 = new Repository_persist_recursion_linked5_Entity;
		$e1->{'11'} = $e2;
		$this->m->Repository_persist_recursion_linked4_->persist($e1);
		$this->assertSame(array(
			'Repository_persist_recursion_linked4_Repository',
			'Repository_persist_recursion_linked5_Repository',
			'Repository_persist_recursion_linked4_Repository',
		), $this->c);
		$this->assertFalse($e1->isChanged());
		$this->assertFalse($e2->isChanged());
	}

	public function test11WithNull2()
	{
		$this->m->Repository_persist_recursion4_->events->addCallbackListener(Events::PERSIST, array($this, 'persistCount'));
		$this->m->Repository_persist_recursion5_->events->addCallbackListener(Events::PERSIST, array($this, 'persistCount'));
		$e1 = new Repository_persist_recursion4_Entity;
		$e2 = new Repository_persist_recursion5_Entity;
		$e1->{'11'} = $e2;
		$e2->{'11'} = $e1;
		$this->m->Repository_persist_recursion5_->persist($e2);
		$this->assertSame(array(
			'Repository_persist_recursion4_Repository',
			'Repository_persist_recursion5_Repository',
			'Repository_persist_recursion4_Repository',
		), $this->c);
		$this->assertFalse($e1->isChanged());
		$this->assertFalse($e2->isChanged());
	}

	public function test11LinkedWithNull2()
	{
		$this->m->Repository_persist_recursion_linked4_->events->addCallbackListener(Events::PERSIST, array($this, 'persistCount'));
		$this->m->Repository_persist_recursion_linked5_->events->addCallbackListener(Events::PERSIST, array($this, 'persistCount'));
		$e1 = new Repository_persist_recursion_linked4_Entity;
		$e2 = new Repository_persist_recursion_linked5_Entity;
		$e1->{'11'} = $e2;
		$this->m->Repository_persist_recursion_linked5_->persist($e2);
		$this->assertSame(array(
			'Repository_persist_recursion_linked4_Repository',
			'Repository_persist_recursion_linked5_Repository',
			'Repository_persist_recursion_linked4_Repository',
		), $this->c);
		$this->assertFalse($e1->isChanged());
		$this->assertFalse($e2->isChanged());
	}

	public function test11WithNullHasId()
	{
		$this->m->Repository_persist_recursion4_->events->addCallbackListener(Events::PERSIST, array($this, 'persistCount'));
		$this->m->Repository_persist_recursion5_->events->addCallbackListener(Events::PERSIST, array($this, 'persistCount'));
		$e1 = new Repository_persist_recursion4_Entity;
		$e2 = new Repository_persist_recursion5_Entity;
		$this->m->Repository_persist_recursion4_->persist($e1);
		$e1->{'11'} = $e2;
		$e2->{'11'} = $e1;
		$this->m->Repository_persist_recursion4_->persist($e1);
		$this->assertSame(array(
			'Repository_persist_recursion4_Repository',
			'Repository_persist_recursion5_Repository',
			'Repository_persist_recursion4_Repository',
		), $this->c);
		$this->assertFalse($e1->isChanged());
		$this->assertFalse($e2->isChanged());
	}

	public function test11LinkedWithNullHasId()
	{
		$this->m->Repository_persist_recursion_linked4_->events->addCallbackListener(Events::PERSIST, array($this, 'persistCount'));
		$this->m->Repository_persist_recursion_linked5_->events->addCallbackListener(Events::PERSIST, array($this, 'persistCount'));
		$e1 = new Repository_persist_recursion_linked4_Entity;
		$e2 = new Repository_persist_recursion_linked5_Entity;
		$this->m->Repository_persist_recursion_linked4_->persist($e1);
		$e1->{'11'} = $e2;
		$this->m->Repository_persist_recursion_linked4_->persist($e1);
		$this->assertSame(array(
			'Repository_persist_recursion_linked4_Repository',
			'Repository_persist_recursion_linked5_Repository',
			'Repository_persist_recursion_linked4_Repository',
		), $this->c);
		$this->assertFalse($e1->isChanged());
		$this->assertFalse($e2->isChanged());
	}

	public function test11WithNull3()
	{
		$this->m->Repository_persist_recursion6_->events->addCallbackListener(Events::PERSIST, array($this, 'persistCount'));
		$this->m->Repository_persist_recursion7_->events->addCallbackListener(Events::PERSIST, array($this, 'persistCount'));
		$this->m->Repository_persist_recursion8_->events->addCallbackListener(Events::PERSIST, array($this, 'persistCount'));
		$this->m->Repository_persist_recursion9_->events->addCallbackListener(Events::PERSIST, array($this, 'persistCount'));
		$e1 = new Repository_persist_recursion6_Entity;
		$e2 = new Repository_persist_recursion7_Entity;
		$e3 = new Repository_persist_recursion8_Entity;
		$e4 = new Repository_persist_recursion9_Entity;
		$e1->{'11'} = $e2;
		$e2->{'11'} = $e3;
		$e3->{'11'} = $e4;
		$e4->{'11'} = $e1;
		$this->m->Repository_persist_recursion6_->persist($e1);
		$this->assertSame(array(
			'Repository_persist_recursion9_Repository',
			'Repository_persist_recursion8_Repository',
			'Repository_persist_recursion7_Repository',
			'Repository_persist_recursion6_Repository',
			'Repository_persist_recursion9_Repository',
		), $this->c);
		$this->assertFalse($e1->isChanged());
		$this->assertFalse($e2->isChanged());
		$this->assertFalse($e3->isChanged());
		$this->assertFalse($e4->isChanged());
	}

	public function testOverMore()
	{
		$e1 = new Repository_persist_recursion1_Entity;
		$e2 = new Repository_persist_recursion1_Entity;
		$e3 = new Repository_persist_recursion1_Entity;
		$e1->m1 = $e2;
		$e2->m1 = $e3;
		$e3->m1 = $e1;
		$this->setExpectedException('Orm\RecursiveException', 'There is an infinite recursion during persist in Repository_persist_recursion1_Entity', 2);
		$this->r1->persist($e1);
	}

	public function testMM()
	{
		$e = new Repository_persist_recursion2_Entity;
		$this->r2->attach($e);
		$e->mma->add($e);
		$e->mmb->add($e);
		$this->r2->persist($e);
		$this->assertTrue(true); // mm se nezacykly
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

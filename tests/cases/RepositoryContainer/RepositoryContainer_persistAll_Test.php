<?php

use Orm\RepositoryContainer;
use Orm\Events;

/**
 * @covers Orm\RepositoryContainer::persistAll
 * @covers Orm\RepositoryContainer::checkRepository
 */
class RepositoryContainer_persistAll_Test extends TestCase
{
	private $m;
	private $r;

	protected function setUp()
	{
		$this->m = new RepositoryContainer;
		$r2 = $this->m->Repository_persist2_;
		$e = $r2->mapper->findAll()->fetchAll();
		$e[0]->markAsChanged();
		$this->r = $this->m->Repository_persist_;
	}

	public function testNone()
	{
		$this->m->persistAll();
		$this->assertSame(0, $this->r->mapper->count);
	}

	public function testNoneHasLoad()
	{
		$this->r->mapper->findAll()->fetchAll();
		$this->m->persistAll();
		$this->assertSame(0, $this->r->mapper->count);
	}

	public function testChanged()
	{
		$es = $this->r->mapper->findAll()->fetchAll();
		$es[0]->markAsChanged();
		$this->m->persistAll();
		$this->assertSame(1, $this->r->mapper->count);
		$this->assertSame(false, $es[0]->isChanged());
	}

	public function testNew()
	{
		$e = $this->r->attach(new TestEntity);
		$this->m->persistAll();
		$this->assertSame(1, $this->r->mapper->count);
		$this->assertSame(3, $e->id);
		$this->assertSame(false, $e->isChanged());
	}

	public function testNewAndChanged()
	{
		$es = $this->r->mapper->findAll()->fetchAll();
		$e = $this->r->attach(new TestEntity);
		$es[0]->markAsChanged();
		$this->m->persistAll();
		$this->assertSame(2, $this->r->mapper->count);
		$this->assertSame(false, $es[0]->isChanged());
		$this->assertSame(3, $e->id);
		$this->assertSame(false, $e->isChanged());
	}

	public function testNewAndRemove()
	{
		$e = $this->r->attach(new TestEntity);
		$this->r->remove($e);
		$this->m->persistAll();
		$this->assertSame(0, $this->r->mapper->count);
		$this->assertSame(true, $e->isChanged());
	}

	public function testChangedAndRemove()
	{
		$es = $this->r->mapper->findAll()->fetchAll();
		$es[0]->markAsChanged();
		$this->r->remove($es[0]);
		$this->m->persistAll();
		$this->assertSame(0, $this->r->mapper->count);
		$this->assertSame(true, $es[0]->isChanged());
	}

	public function testNewChanged()
	{
		$e = $this->r->persist(new TestEntity);
		$this->assertSame(1, $this->r->mapper->count);
		$e->markAsChanged();
		$this->m->persistAll();
		$this->assertSame(2, $this->r->mapper->count);
		$this->assertSame(3, $e->id);
		$this->assertSame(false, $e->isChanged());
	}

	public function testNewNotChanged()
	{
		$e = $this->r->persist(new TestEntity);
		$this->assertSame(1, $this->r->mapper->count);
		$this->m->persistAll();
		$this->assertSame(1, $this->r->mapper->count);
		$this->assertSame(3, $e->id);
		$this->assertSame(false, $e->isChanged());
	}

	public function testReturns()
	{
		$this->assertSame(NULL, $this->m->persistAll());
	}

	public function testChangedDuringPersist1()
	{
		$e = $this->r->attach(new TestEntity);
		$persistCount = 0;
		$test = $this;
		$this->r->events->addCallbackListener(Events::PERSIST, function ($args) use (& $persistCount, $test) {
			$persistCount++;
			$test->assertSame(3, $args->id);
		});
		$this->r->events->addCallbackListener(Events::PERSIST_AFTER, function ($args) {
			$args->entity->string .= '_changedDuringPersist';
		});
		$e->string = 'xxx';

		$this->assertSame(0, $this->r->mapper->count);
		$this->assertSame(0, $persistCount);
		$this->m->persistAll();
		$this->assertSame(2, $this->r->mapper->count);
		$this->assertSame(2, $persistCount);
		$this->assertSame(3, $e->id);
		$this->assertSame(false, $e->isChanged());
		$this->assertSame('xxx_changedDuringPersist', $e->string);
	}

	public function testChangedDuringPersist2()
	{
		$e = $this->r->attach(new TestEntity);
		$e2 = $this->r->attach(new TestEntity);
		$e3 = new TestEntity;
		$persistCount = array();
		$this->r->events->addCallbackListener(Events::PERSIST, function ($args) use (& $persistCount) {
			$persistCount[] = $args->id;
		});
		$this->r->events->addCallbackListener(Events::PERSIST_AFTER, function ($args) use ($e2, $e3) {
			$e2->string .= '_changedDuringPersist' . $args->entity->id;
			$args->entity->repository->attach($e3);
		});
		$e->string = 'aaa';
		$e2->string = 'bbb';
		$e3->string = 'ccc';

		$this->assertSame(0, $this->r->mapper->count);
		$this->assertSame(array(), $persistCount);
		$this->m->persistAll();
		$this->assertSame(array(
			3, // normalni
			4, // normalni
			4, // upraveno pri PERSIST_AFTER v ramci persist se vola rovnou mapper
			5, // pridala se v ramci PERSIST_AFTER dopersistuje se v druhe iteraci
			4, // upraveno pri PERSIST_AFTER v ramci persistAll se vola rovnou mapper
		), $persistCount);
		$this->assertSame(5, $this->r->mapper->count);
		$this->assertSame(3, $e->id);
		$this->assertSame(4, $e2->id);
		$this->assertSame(5, $e3->id);
		$this->assertSame(false, $e->isChanged());
		$this->assertSame(false, $e2->isChanged());
		$this->assertSame(false, $e3->isChanged());
		$this->assertSame('aaa', $e->string);
		$this->assertSame('bbb_changedDuringPersist3_changedDuringPersist4_changedDuringPersist5', $e2->string);
		$this->assertSame('ccc', $e3->string);
	}

	public function testRemoveDuringPersist1()
	{
		$r = $this->r;
		$r2 = $this->m->Repository_persist2_;
		$e = $r->attach(new TestEntity);
		$e2 = $r2->attach(new Repository_persist_Entity);
		$order = array();
		$test = $this;
		$r->events->addCallbackListener(Events::PERSIST, function ($args) use (& $order, $test) {
			$order[] = 'persist';
			$test->assertSame(3, $args->id);
		});
		$r->events->addCallbackListener(Events::REMOVE_BEFORE, function ($args) use (& $order, $test) {
			$order[] = 'remove';
		});
		$r2->events->addCallbackListener(Events::PERSIST, function ($args) use (& $order, $test) {
			$order[] = 'persist2_' . $args->id;
		});
		$r2->events->addCallbackListener(Events::REMOVE_BEFORE, function ($args) use (& $order, $test) {
			$order[] = 'remove2';
		});
		$r->events->addCallbackListener(Events::PERSIST_AFTER, function ($args) use ($r2, $e2) {
			$r2->remove($e2);
		});

		$this->assertSame(0, $r->mapper->count);
		$this->assertSame(0, $r2->mapper->count);
		$this->assertSame(array(), $order);
		$this->m->persistAll();
		$this->assertSame(1, $this->r->mapper->count);
		$this->assertSame(4, $r2->mapper->count);
		$this->assertSame(array(
			'persist2_3',
			'persist2_3',
			'persist2_1',
			'persist2_1',
			'persist',
			'remove2',
		), $order);
		$this->assertSame(3, $e->id);
		$this->assertSame(false, $e->isChanged());
		$this->assertSame(false, isset($e2->id));
		$this->assertSame(true, $e2->isChanged());
	}

	public function testRemoveDuringPersist2()
	{
		$r = $this->r;
		$e = $this->r->attach(new TestEntity);
		$order = array();
		$test = $this;
		$this->r->events->addCallbackListener(Events::PERSIST, function ($args) use (& $order, $test) {
			$order[] = 'persist';
			$test->assertSame(3, $args->id);
		});
		$this->r->events->addCallbackListener(Events::REMOVE_BEFORE, function ($args) use (& $order, $test) {
			$order[] = 'remove';
		});
		$this->r->events->addCallbackListener(Events::PERSIST_AFTER, function ($args) use ($r) {
			$r->remove($args->entity);
		});

		$this->assertSame(0, $this->r->mapper->count);
		$this->assertSame(array(), $order);
		$this->m->persistAll();
		$this->assertSame(1, $this->r->mapper->count);
		$this->assertSame(array('persist', 'remove'), $order);
		$this->assertSame(false, isset($e->id));
		$this->assertSame(true, $e->isChanged());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RepositoryContainer', 'persistAll');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

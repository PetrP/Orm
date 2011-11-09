<?php

use Orm\Events;
use Orm\RepositoryContainer;

/**
 * @covers Orm\Repository::clean
 */
class Repository_clean_Test extends TestCase
{
	private $m;
	private $r;
	private $r2;

	protected function setUp()
	{
		$this->m = new Repository_clean_Model;
		$this->r = $this->m->Repository_clean_;
		$this->r2 = $this->m->Repository_clean2_;
	}

	public function test()
	{
		$this->assertSame(0, $this->m->count);
		$this->assertSame(0, $this->r->mapper->count);
		$this->assertSame(0, $this->r2->mapper->count);
		$this->r->clean();
		$this->assertSame(1, $this->m->count);
		$this->assertSame(1, $this->r->mapper->count);
		$this->assertSame(1, $this->r2->mapper->count);
	}

	public function testDeprecated1()
	{
		$this->setExpectedException('Orm\DeprecatedException', 'Orm\Repository::clean(TRUE) is deprecated.');
		$this->r->clean(true);
	}

	public function testDeprecated2()
	{
		$this->r->clean(false);
		$this->assertTrue(true);
	}

	public function testNotHandleByModel()
	{
		$r = new Repository_clean_Repository(new RepositoryContainer);
		$this->setExpectedException('Orm\RepositoryNotFoundException', "Repository 'Repository_clean_Repository' is not attached to RepositoryContainer. It is impossible clean it. Do not inicialize your own repository, but ask RepositoryContainer for it.");
		$r->clean();
	}

	public function testClearAllEntity()
	{
		$e = new TestEntity;
		$e->string = 'xyz';
		$this->r->persist($e);
		$this->r->clean();
		$this->assertSame('xyz', $e->string);
		$this->assertTrue(isset($e->id)); // bug?
		$this->assertSame($e, $this->r->getById(3)); // bug?
	}

	public function testClearAllEntity2()
	{
		$e = $this->r->getById(1);
		$e->string = 'xyz';
		$this->r->persist($e);
		$this->r->clean();
		$this->assertSame(1, $e->id);
		$this->assertSame($e, $this->r->getById(1));
		$this->assertSame('xyz', $e->string); // bug?
	}

	public function testEvents1()
	{
		$test = $this;
		$order = (object) array('a' => array());
		$r = $this->r;
		$r->events->addCallbackListener(Events::CLEAN_BEFORE, function () use ($r, $order, $test) {
			$test->assertSame(0, $r->mapper->count);
			$order->a[] = 'before';
		});
		$r->events->addCallbackListener(Events::CLEAN_AFTER, function () use ($r, $order, $test) {
			$test->assertSame(1, $r->mapper->count);
			$order->a[] = 'after';
		});
		$this->assertSame(0, $r->mapper->count);
		$r->clean();
		$this->assertSame(1, $r->mapper->count);
		$this->assertSame(array('before', 'after'), $order->a);
	}

	public function testEvents2()
	{
		$test = $this;
		$order = (object) array('a' => array());
		$r1 = $this->r;
		$r2 = $this->r2;
		$r1->events->addCallbackListener(Events::CLEAN_BEFORE, function () use ($r1, $r2, $order, $test) {
			$test->assertSame(0, $r1->mapper->count);
			$test->assertSame(0, $r2->mapper->count);
			$order->a[] = '1_before';
		});
		$r1->events->addCallbackListener(Events::CLEAN_AFTER, function () use ($r1, $r2, $order, $test) {
			$test->assertSame(1, $r1->mapper->count);
			$test->assertSame(1, $r2->mapper->count);
			$order->a[] = '1_after';
		});
		$r2->events->addCallbackListener(Events::CLEAN_BEFORE, function () use ($r1, $r2, $order, $test) {
			$test->assertSame(0, $r1->mapper->count);
			$test->assertSame(0, $r2->mapper->count);
			$order->a[] = '2_before';
		});
		$r2->events->addCallbackListener(Events::CLEAN_AFTER, function () use ($r1, $r2, $order, $test) {
			$test->assertSame(1, $r1->mapper->count);
			$test->assertSame(1, $r2->mapper->count);
			$order->a[] = '2_after';
		});

		$this->assertSame(0, $r1->mapper->count);
		$this->assertSame(0, $r2->mapper->count);
		$r1->clean();
		$this->assertSame(1, $r1->mapper->count);
		$this->assertSame(1, $r2->mapper->count);
		$this->assertSame(array('1_before', '2_before', '1_after', '2_after'), $order->a);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Repository', 'clean');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

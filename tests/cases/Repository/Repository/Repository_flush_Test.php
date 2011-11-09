<?php

use Orm\Events;
use Orm\RepositoryContainer;

/**
 * @covers Orm\Repository::flush
 */
class Repository_flush_Test extends TestCase
{
	private $m;
	private $r;
	private $r2;

	protected function setUp()
	{
		$this->m = new Repository_flush_Model;
		$this->r = $this->m->Repository_flush_;
		$this->r2 = $this->m->Repository_flush2_;
	}

	public function test()
	{
		$this->assertSame(0, $this->m->count);
		$this->assertSame(0, $this->r->mapper->count);
		$this->assertSame(0, $this->r2->mapper->count);
		$this->r->flush();
		$this->assertSame(1, $this->m->count);
		$this->assertSame(1, $this->r->mapper->count);
		$this->assertSame(1, $this->r2->mapper->count);
	}

	public function testDeprecated1()
	{
		$this->setExpectedException('Orm\DeprecatedException', 'Orm\Repository::flush(TRUE) is deprecated.');
		$this->r->flush(true);
	}

	public function testDeprecated2()
	{
		$this->r->flush(false);
		$this->assertTrue(true);
	}

	public function testNotHandleByModel()
	{
		$r = new Repository_flush_Repository(new RepositoryContainer);
		$this->setExpectedException('Orm\RepositoryNotFoundException', "Repository 'Repository_flush_Repository' is not attached to RepositoryContainer. It is impossible flush it. Do not inicialize your own repository, but ask RepositoryContainer for it.");
		$r->flush();
	}

	public function testEvents1()
	{
		$test = $this;
		$order = (object) array('a' => array());
		$r = $this->r;
		$r->events->addCallbackListener(Events::FLUSH_BEFORE, function () use ($r, $order, $test) {
			$test->assertSame(0, $r->mapper->count);
			$order->a[] = 'before';
		});
		$r->events->addCallbackListener(Events::FLUSH_AFTER, function () use ($r, $order, $test) {
			$test->assertSame(1, $r->mapper->count);
			$order->a[] = 'after';
		});
		$this->assertSame(0, $r->mapper->count);
		$r->flush();
		$this->assertSame(1, $r->mapper->count);
		$this->assertSame(array('before', 'after'), $order->a);
	}

	public function testEvents2()
	{
		$test = $this;
		$order = (object) array('a' => array());
		$r1 = $this->r;
		$r2 = $this->r2;
		$r1->events->addCallbackListener(Events::FLUSH_BEFORE, function () use ($r1, $r2, $order, $test) {
			$test->assertSame(0, $r1->mapper->count);
			$test->assertSame(0, $r2->mapper->count);
			$order->a[] = '1_before';
		});
		$r1->events->addCallbackListener(Events::FLUSH_AFTER, function () use ($r1, $r2, $order, $test) {
			$test->assertSame(1, $r1->mapper->count);
			$test->assertSame(1, $r2->mapper->count);
			$order->a[] = '1_after';
		});
		$r2->events->addCallbackListener(Events::FLUSH_BEFORE, function () use ($r1, $r2, $order, $test) {
			$test->assertSame(0, $r1->mapper->count);
			$test->assertSame(0, $r2->mapper->count);
			$order->a[] = '2_before';
		});
		$r2->events->addCallbackListener(Events::FLUSH_AFTER, function () use ($r1, $r2, $order, $test) {
			$test->assertSame(1, $r1->mapper->count);
			$test->assertSame(1, $r2->mapper->count);
			$order->a[] = '2_after';
		});

		$this->assertSame(0, $r1->mapper->count);
		$this->assertSame(0, $r2->mapper->count);
		$r1->flush();
		$this->assertSame(1, $r1->mapper->count);
		$this->assertSame(1, $r2->mapper->count);
		$this->assertSame(array('1_before', '2_before', '1_after', '2_after'), $order->a);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Repository', 'flush');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

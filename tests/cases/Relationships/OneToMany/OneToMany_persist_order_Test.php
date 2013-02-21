<?php

use Orm\RepositoryContainer;


/**
 * @covers Orm\OneToMany::persist
 * @covers Orm\OneToMany::applyOrderValue
 */
class OneToMany_persist_order_Test extends TestCase
{
	private $r;
	private $e;

	protected function setUp()
	{
		parent::setUp();
		$m = new RepositoryContainer;
		$this->r = $m->getRepository('OneToMany_persist_order_1_Repository');
		$this->e = $this->r->attach(new OneToMany_persist_order_1_Entity);
		$m->flush();
	}

	private function c($string)
	{
		$e = new OneToMany_persist_order_2_Entity;
		$e->string = $string;
		return $e;
	}

	private function t()
	{
		$this->r->flush();
		foreach ($this->e->many->get() as $e)
		{
			$this->assertSame((int) $e->string, $e->order);
		}
	}

	public function test1()
	{
		$this->e->many = array(
			$this->c(1),
			$this->c(2),
			$this->c(3),
		);
		$this->t();
	}

	public function test2()
	{
		$this->e->many->add($this->c(1));
		$this->e->many->add($this->c(2));
		$this->e->many->add($this->c(3));
		$this->t();
	}

	public function test3()
	{
		$this->e->many->set(array($this->c(1), $this->c(2)));
		$this->e->many->add($this->c(3));
		$this->e->many->add($this->c(4));
		$this->t();
	}

	public function test4()
	{
		$this->e->many->set(array($this->c(1), $this->c(2)));
		$this->e->many->add($r = $this->c(0));
		$this->e->many->add($this->c(3));
		$this->e->many->remove($r);
		$this->t();
	}

	public function test5()
	{
		$this->e->many->add($this->c(1));
		$this->e->many->add($this->c(2));
		$this->e->many->add($r = $this->c(0));
		$this->e->many->add($this->c(3));
		$this->e->many->remove($r);
		$this->t();
	}

	public function testDiferentProperty()
	{
		$this->e->many->orderProperty = 'order2';
		$this->e->many->add($this->c(1));
		$this->e->many->add($this->c(2));
		$this->e->many->add($this->c(3));
		$this->r->flush();
		foreach ($this->e->many->get() as $e)
		{
			$this->assertSame(NULL, $e->order);
			$this->assertSame((int) $e->string, $e->order2);
		}
	}

	public function testDisabled()
	{
		$this->e->many->orderProperty = NULL;
		$this->e->many->add($this->c(1));
		$this->e->many->add($this->c(2));
		$this->e->many->add($this->c(3));
		$this->r->flush();
		foreach ($this->e->many->get() as $e)
		{
			$this->assertSame(NULL, $e->order);
			$this->assertSame(NULL, $e->order2);
		}
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\OneToMany', 'applyOrderValue');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

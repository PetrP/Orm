<?php

use Orm\RepositoryContainer;


/**
 * @covers Orm\OneToMany::getOrderProperty
 */
class OneToMany_getOrderProperty_Test extends TestCase
{

	public function testDefaultHasOrderProperty()
	{
		$m = new RepositoryContainer;
		$r = $m->getRepository('OneToMany_persist_order_1_Repository');
		$e = $r->attach(new OneToMany_persist_order_1_Entity);
		$this->assertSame('order', Access($e->many)->getOrderProperty());
	}

	public function testDefaultHasNotOrderProperty()
	{
		$m = new RepositoryContainer;
		$r = $m->getRepository('OneToManyX_Repository');
		$e = $r->attach(new OneToManyX_Entity);
		$this->assertSame(NULL, Access($e->many)->getOrderProperty());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\OneToMany', 'getOrderProperty');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

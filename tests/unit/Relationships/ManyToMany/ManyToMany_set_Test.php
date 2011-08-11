<?php

/**
 * @covers Orm\ManyToMany::set
 */
class ManyToMany_set_Test extends ManyToMany_Test
{

	public function test()
	{
		$e = new OneToMany_Entity;
		$this->m2m->set(array($e, 11));
		$this->t($e, 11);
	}

	public function testNull()
	{
		$this->m2m->set(array(NULL));
		$this->t();
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\BaseToMany', 'set');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

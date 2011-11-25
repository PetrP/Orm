<?php

/**
 * @covers Orm\OneToMany::getParent
 */
class OneToMany_getParent_Test extends OneToMany_Test
{

	public function test()
	{
		$this->assertSame($this->e, $this->o2m->_getParent());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\OneToMany', 'getParent');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

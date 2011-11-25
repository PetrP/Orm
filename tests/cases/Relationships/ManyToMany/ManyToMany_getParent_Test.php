<?php

/**
 * @covers Orm\ManyToMany::getParent
 */
class ManyToMany_getParent_Test extends ManyToMany_Test
{

	public function test()
	{
		$this->assertSame($this->e, $this->m2m->_getParent());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ManyToMany', 'getParent');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

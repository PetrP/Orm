<?php

/**
 * @covers Orm\OneToMany::getInjectedValue
 */
class OneToMany_getInjectedValue_Test extends OneToMany_Test
{

	public function test()
	{
		$this->assertNull($this->o2m->getInjectedValue());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\BaseToMany', 'getInjectedValue');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

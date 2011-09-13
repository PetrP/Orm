<?php

/**
 * @covers Orm\ManyToMany::handleCheckAndIgnore
 */
class ManyToMany_handleCheckAndIgnore_Test extends ManyToMany_Test
{

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ManyToMany', 'handleCheckAndIgnore');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

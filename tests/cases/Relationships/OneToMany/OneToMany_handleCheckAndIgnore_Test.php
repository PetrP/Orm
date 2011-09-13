<?php

/**
 * @covers Orm\ManyToMany::handleCheckAndIgnore
 */
class OneToMany_handleCheckAndIgnore_Test extends OneToMany_Test
{

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\OneToMany', 'handleCheckAndIgnore');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

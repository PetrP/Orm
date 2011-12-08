<?php

/**
 * @covers Orm\DibiManyToManyMapper::validateInjectedValue
 */
class DibiManyToManyMapper_validateInjectedValue_Test extends DibiManyToManyMapper_Connected_Test
{

	public function test()
	{
		$this->assertSame(NULL, $this->mm->validateInjectedValue(array(1 => 1)));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DibiManyToManyMapper', 'validateInjectedValue');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

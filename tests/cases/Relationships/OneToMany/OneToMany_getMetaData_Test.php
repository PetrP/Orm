<?php

/**
 * @covers Orm\OneToMany::getMetaData
 */
class OneToMany_getMetaData_Test extends OneToMany_Test
{

	public function test()
	{
		$this->o2m = new OneToMany_OneToMany($this->e, $this->meta1);
		$this->assertSame($this->meta1, $this->o2m->_getMetaData());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\OneToMany', 'getMetaData');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

<?php

/**
 * @covers Orm\ManyToMany::getMetaData
 */
class ManyToMany_getMetaData_Test extends ManyToMany_Test
{

	public function test()
	{
		$this->m2m = new ManyToMany_ManyToMany($this->e, $this->meta1);
		$this->assertSame($this->meta1, $this->m2m->_getMetaData());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ManyToMany', 'getMetaData');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

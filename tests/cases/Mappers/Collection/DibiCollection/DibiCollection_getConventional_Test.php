<?php

/**
 * @covers Orm\DibiCollection::getConventional
 */
class DibiCollection_getConventional_Test extends DibiCollection_Base_Test
{

	public function test()
	{
		$this->assertInstanceOf('Orm\IConventional', DibiCollection_DibiCollection::call($this->c, 'getConventional'));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\BaseDibiCollection', 'getConventional');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

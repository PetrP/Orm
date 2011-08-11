<?php

/**
 * @covers Orm\DibiCollection::getMapper
 */
class DibiCollection_getMapper_Test extends DibiCollection_Base_Test
{

	public function test()
	{
		$this->assertInstanceOf('Orm\DibiMapper', DibiCollection_DibiCollection::call($this->c, 'getMapper'));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\BaseDibiCollection', 'getMapper');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

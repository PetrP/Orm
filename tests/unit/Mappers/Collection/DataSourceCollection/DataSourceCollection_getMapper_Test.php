<?php

/**
 * @covers Orm\DataSourceCollection::getMapper
 */
class DataSourceCollection_getMapper_Test extends DataSourceCollection_Base_Test
{

	public function test()
	{
		$this->assertInstanceOf('Orm\DibiMapper', DataSourceCollection_DataSourceCollection::call($this->c, 'getMapper'));
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

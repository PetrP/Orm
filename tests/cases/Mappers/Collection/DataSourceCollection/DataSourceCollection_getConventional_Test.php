<?php

/**
 * @covers Orm\DataSourceCollection::getConventional
 */
class DataSourceCollection_getConventional_Test extends DataSourceCollection_Base_Test
{

	public function test()
	{
		$this->assertInstanceOf('Orm\IConventional', DataSourceCollection_DataSourceCollection::call($this->c, 'getConventional'));
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

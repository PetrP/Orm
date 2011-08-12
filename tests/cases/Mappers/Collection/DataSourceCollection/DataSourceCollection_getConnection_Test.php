<?php

/**
 * @covers Orm\DataSourceCollection::getConnection
 */
class DataSourceCollection_getConnection_Test extends DataSourceCollection_Base_Test
{

	public function test()
	{
		$this->assertInstanceOf('DibiConnection', DataSourceCollection_DataSourceCollection::call($this->c, 'getConnection'));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\BaseDibiCollection', 'getConnection');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

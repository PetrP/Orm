<?php

/**
 * @covers Orm\DataSourceCollection::getRepository
 */
class DataSourceCollection_getRepository_Test extends DataSourceCollection_Base_Test
{

	public function test()
	{
		$this->assertInstanceOf('Orm\IRepository', DataSourceCollection_DataSourceCollection::call($this->c, 'getRepository'));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\BaseDibiCollection', 'getRepository');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

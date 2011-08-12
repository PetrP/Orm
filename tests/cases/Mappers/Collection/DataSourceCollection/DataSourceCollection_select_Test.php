<?php

/**
 * @covers Orm\DataSourceCollection::select
 */
class DataSourceCollection_select_Test extends DataSourceCollection_Base_Test
{

	public function test()
	{
		$this->setExpectedException('Nette\DeprecatedException', 'DataSourceCollection::select() is deprecated; use DataSourceCollection->getDataSource()->select() instead');
		$this->c->select('foo');
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DataSourceCollection', 'select');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

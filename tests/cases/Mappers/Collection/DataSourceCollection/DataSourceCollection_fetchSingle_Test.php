<?php

/**
 * @covers Orm\DataSourceCollection::fetchSingle
 */
class DataSourceCollection_fetchSingle_Test extends DataSourceCollection_Base_Test
{

	public function test()
	{
		$this->setExpectedException('Nette\DeprecatedException', 'DataSourceCollection::fetchSingle() is deprecated; use DataSourceCollection->getDataSource()->fetchSingle() instead');
		$this->c->fetchSingle();
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DataSourceCollection', 'fetchSingle');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

<?php

/**
 * @covers Orm\DataSourceCollection::getTotalCount
 */
class DataSourceCollection_getTotalCount_Test extends DataSourceCollection_Base_Test
{

	public function test()
	{
		$this->setExpectedException('Orm\NotImplementedException');
		$this->c->getTotalCount();
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DataSourceCollection', 'getTotalCount');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

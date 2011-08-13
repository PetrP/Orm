<?php

/**
 * @covers Orm\DataSourceCollection::join
 */
class DataSourceCollection_join_Test extends DataSourceCollection_Base_Test
{

	public function test()
	{
		$this->setExpectedException('Orm\NotSupportedException', 'Joins are not supported for DataSourceCollection');
		$this->c->join('foo->bar');
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DataSourceCollection', 'join');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

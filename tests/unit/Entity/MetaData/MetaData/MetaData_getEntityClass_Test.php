<?php

use Orm\MetaData;

/**
 * @covers Orm\MetaData::getEntityClass
 */
class MetaData_getEntityClass_Test extends TestCase
{
	public function testEntity()
	{
		$m = new MetaData(new MetaDATA_Test_Entity);
		$this->assertSame('MetaData_Test_Entity', $m->getEntityClass());
	}

	public function testString()
	{
		$m = new MetaData('MetaDATA_Test_Entity');
		$this->assertSame('MetaData_Test_Entity', $m->getEntityClass());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\MetaData', 'getEntityClass');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

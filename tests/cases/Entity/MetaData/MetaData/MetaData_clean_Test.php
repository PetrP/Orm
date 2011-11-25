<?php

use Orm\MetaData;

/**
 * @covers Orm\MetaData::clean
 */
class MetaData_clean_Test extends TestCase
{

	public function test()
	{
		MetaData::getEntityRules('MetaData_Test_Entity');
		$this->assertAttributeNotEmpty('cache', 'Orm\MetaData');
		MetaData::clean();
		$this->assertAttributeEmpty('cache', 'Orm\MetaData');
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\MetaData', 'clean');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertTrue($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

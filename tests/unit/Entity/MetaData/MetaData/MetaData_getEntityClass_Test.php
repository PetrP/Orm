<?php

require_once __DIR__ . '/../../../../boot.php';

/**
 * @covers MetaData::getEntityClass
 */
class MetaData_getEntityClass_Test extends TestCase
{
	public function testEntity()
	{
		$m = new MetaData(new MetaDATA_Test_Entity);
		$this->assertEquals('MetaData_Test_Entity', $m->getEntityClass());
	}

	public function testString()
	{
		$m = new MetaData('MetaDATA_Test_Entity');
		$this->assertEquals('MetaData_Test_Entity', $m->getEntityClass());
	}

}

<?php

require_once __DIR__ . '/../../../../boot.php';

class MetaData_getEntityRules_Test extends TestCase
{
	public function testCache()
	{
		MetaData_Test_Entity::$count = 0;
		MetaData::getEntityRules('MeTaData_Test_Entity');
		MetaData::getEntityRules('MeTaData_Test_Entity');
		MetaData::getEntityRules('MeTaData_Test_Entity');
		$this->assertEquals(1, MetaData_Test_Entity::$count);
	}

	public function testNotExists()
	{
		try {
			MetaData::getEntityRules('Xxxasdsad');
		} catch (Exception $e) {}
		$this->assertException($e, 'InvalidStateException', "Class 'Xxxasdsad' doesn`t exists");
	}

	public function testNotEntity()
	{
		try {
			MetaData::getEntityRules('Html');
		} catch (Exception $e) {}
		$this->assertException($e, 'InvalidStateException', "'Html' isn`t instance of IEntity");
	}

	public function testBadReturn()
	{
		MetaData_Test_Entity::$metaData = new Html;
		try {
			MetaData::getEntityRules('MEtaData_Test_Entity');
		} catch (Exception $e) {}
		$this->assertException($e, 'InvalidStateException', "It`s expected that 'IEntity::createMetaData' will return 'MetaData'.");
		MetaData_Test_Entity::$metaData = NULL;
	}

	public function testReturn()
	{
		$this->assertInternalType('array', MetaData::getEntityRules('MetaData_Test_Entity'));
	}
}

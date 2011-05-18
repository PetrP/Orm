<?php

use Nette\Utils\Html;
use Orm\MetaData;

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers MetaData::getEntityRules
 */
class MetaData_getEntityRules_Test extends TestCase
{
	protected function setUp()
	{
		MetaData::clean();
		MetaData_Test_Entity::$metaData = NULL;
	}

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
		$this->setExpectedException('InvalidStateException', "Class 'Xxxasdsad' doesn`t exists");
		MetaData::getEntityRules('Xxxasdsad');
	}

	public function testNotEntity()
	{
		$this->setExpectedException('InvalidStateException', "'Html' isn`t instance of IEntity");
		MetaData::getEntityRules('Html');
	}

	public function testBadReturn()
	{
		$this->setExpectedException('InvalidStateException', "It`s expected that 'IEntity::createMetaData' will return 'MetaData'.");
		MetaData_Test_Entity::$metaData = new Html;
		MetaData::getEntityRules('MetaData_Test_Entity');
	}

	public function testReturn()
	{
		$this->assertInternalType('array', MetaData::getEntityRules('MetaData_Test_Entity'));
	}
}

<?php

use Orm\MetaData;

/**
 * @covers Orm\MetaData::getEntityRules
 * @covers Orm\MetaData::createEntityRules
 */
class MetaData_getEntityRules_NoRepoCon_Test extends TestCase
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
		$this->assertSame(1, MetaData_Test_Entity::$count);
	}

	public function testNotExists()
	{
		$this->setExpectedException('Nette\InvalidStateException', "Class 'Xxxasdsad' doesn`t exists");
		MetaData::getEntityRules('Xxxasdsad');
	}

	public function testNotEntity()
	{
		$this->setExpectedException('Nette\InvalidStateException', "'Directory' isn`t instance of Orm\\IEntity");
		MetaData::getEntityRules('Directory');
	}

	public function testBadReturn()
	{
		$this->setExpectedException('Nette\InvalidStateException', "It`s expected that 'Orm\\IEntity::createMetaData' will return 'Orm\\MetaData'.");
		MetaData_Test_Entity::$metaData = new Directory;
		MetaData::getEntityRules('MetaData_Test_Entity');
	}

	public function testReturn()
	{
		$this->assertInternalType('array', MetaData::getEntityRules('MetaData_Test_Entity'));
	}

	public function testRecursionCache()
	{
		$this->assertAttributeEmpty('cache2', 'Orm\MetaData');
		MetaData::getEntityRules('RelationshipLoader_ManyToMany1_Entity');
		$this->assertAttributeEmpty('cache2', 'Orm\MetaData');
	}

}

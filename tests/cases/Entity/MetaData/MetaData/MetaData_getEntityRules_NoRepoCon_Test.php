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
		$this->setExpectedException('Orm\InvalidEntityException', "Class 'Xxxasdsad' doesn`t exists");
		MetaData::getEntityRules('Xxxasdsad');
	}

	public function testNotEntity()
	{
		$this->setExpectedException('Orm\InvalidEntityException', "'Directory' isn`t instance of Orm\\IEntity");
		MetaData::getEntityRules('Directory');
	}

	public function testBadReturn()
	{
		$this->setExpectedException('Orm\BadReturnException', "MetaData_Test_Entity::createMetaData() must return Orm\\MetaData, 'Directory' given.");
		MetaData_Test_Entity::$metaData = new Directory;
		MetaData::getEntityRules('MetaData_Test_Entity');
	}

	public function testReturn()
	{
		$this->assertInternalType('array', MetaData::getEntityRules('MetaData_Test_Entity'));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\MetaData', 'getEntityRules');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertTrue($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

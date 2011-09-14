<?php

use Orm\MetaData;

/**
 * @covers Orm\MetaData::__construct
 */
class MetaData_construct_Test extends TestCase
{
	public function testEntity()
	{
		new MetaData(new MetaData_Test_Entity);
		$this->assertTrue(true);
	}

	public function testString()
	{
		new MetaData('MetaData_Test_Entity');
		$this->assertTrue(true);
	}

	public function testNotExists()
	{
		$this->setExpectedException('Orm\InvalidArgumentException', "Orm\\MetaData::\$entityClass must be instance of Orm\\IEntity; class 'Xxxasdsad' doesn't exists.");
		new MetaData('Xxxasdsad');
	}

	public function testNotEntity()
	{
		$this->setExpectedException('Orm\InvalidArgumentException', "Orm\\MetaData::\$entityClass must be instance of Orm\\IEntity; 'Directory' given.");
		new MetaData('Directory');
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\MetaData', '__construct');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

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
		$this->setExpectedException('Nette\InvalidStateException', "Class 'Xxxasdsad' doesn`t exists");
		new MetaData('Xxxasdsad');
	}

	public function testNotEntity()
	{
		$this->setExpectedException('Nette\InvalidStateException', "'Nette\\Utils\\Html' isn`t instance of Orm\\IEntity");
		new MetaData('Nette\Utils\Html');
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

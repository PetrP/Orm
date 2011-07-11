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
		try {
			new MetaData('Xxxasdsad');
		} catch (Exception $e) {}
		$this->assertException($e, 'Nette\InvalidStateException', "Class 'Xxxasdsad' doesn`t exists");
	}

	public function testNotEntity()
	{
		$this->setExpectedException('Nette\InvalidStateException', "'Nette\\Utils\\Html' isn`t instance of Orm\\IEntity");
		new MetaData('Nette\Utils\Html');
	}

}

<?php

use Orm\MetaData;

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers MetaData::__construct
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
		$this->assertException($e, 'InvalidStateException', "Class 'Xxxasdsad' doesn`t exists");
	}

	public function testNotEntity()
	{
		try {
			new MetaData('Html');
		} catch (Exception $e) {}
		$this->assertException($e, 'InvalidStateException', "'Html' isn`t instance of IEntity");
	}

}

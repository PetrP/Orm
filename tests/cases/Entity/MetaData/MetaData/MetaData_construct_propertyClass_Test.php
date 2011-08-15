<?php

use Orm\MetaData;

/**
 * @covers Orm\MetaData::__construct
 */
class MetaData_construct_propertyClass_Test extends TestCase
{
	public function testDefault()
	{
		$m = new MetaData(new MetaData_Test_Entity);
		$this->assertAttributeSame('Orm\MetaDataProperty', 'propertyClass', $m);

		$m = new MetaData('MetaData_Test_Entity', NULL);
		$this->assertAttributeSame('Orm\MetaDataProperty', 'propertyClass', $m);

		$m = new MetaData('MetaData_Test_Entity', 'Orm\MetaDataProperty');
		$this->assertAttributeSame('Orm\MetaDataProperty', 'propertyClass', $m);
	}

	public function testBad()
	{
		$this->setExpectedException('Orm\InvalidArgumentException', 'Orm\MetaData::$propertyClass must be subclass of Orm\MetaDataProperty; \'MetaData_construct_propertyClass_Test\' given.');
		new MetaData('MetaData_Test_Entity', __CLASS__);
	}

	public function testOk()
	{
		$m = new MetaData('MetaData_Test_Entity', 'MetaData_construct_propertyClass_MetaDataProperty');
		$this->assertAttributeSame('MetaData_construct_propertyClass_MetaDataProperty', 'propertyClass', $m);

		$p = $m->addProperty('test', 'foo');
		$this->assertInstanceOf('Orm\MetaDataProperty', $p);
		$this->assertInstanceOf('MetaData_construct_propertyClass_MetaDataProperty', $p);
	}

}

<?php

require_once __DIR__ . '/../../../../boot.php';

/**
 * @covers MetaData::addProperty
 */
class MetaData_addProperty_Test extends TestCase
{

	public function testOverwrite()
	{
		$m = new MetaData('MetaData_Test_Entity');

		$m->addProperty('id', 'int1', MetaData::READ);
		$m->addProperty('id', 'int2', MetaData::READ, 'Entity');
		$m->addProperty('id', 'int3', MetaData::READ, 'MetaData_Test_Entity');

		try {
			$m->addProperty('id', 'int4', MetaData::READ, 'MetaData_Test_Entity');
		} catch (Exception $e) {}
		$this->assertException($e, 'Exception', "");

		try {
			$m->addProperty('id', 'int5', MetaData::READ);
		} catch (Exception $e) {}
		$this->assertException($e, 'Exception', "");

		$array = $m->toArray();
		$this->assertEquals(1, count($array));
		$this->assertArrayHasKey('id', $array);
		$this->assertEquals(array('int3' => 'int3'), $array['id']['types']);

		$m->addProperty('id', 'int6', MetaData::READ, 'Neexistuje');

		$array = $m->toArray();
		$this->assertEquals(array('int6' => 'int6'), $array['id']['types']);

	}

	public function testReturn()
	{
		$m = new MetaData('MetaData_Test_Entity');
		$property = $m->addProperty('id', 'int1', MetaData::READ);
		$this->assertInstanceof('MetaDataProperty', $property);
		$this->assertAttributeEquals('id', 'name', $property);
		$this->assertAttributeEquals($m->getEntityClass(), 'class', $property);
		$this->assertAttributeEquals(array('id' => $property), 'properties', $m);
	}

}

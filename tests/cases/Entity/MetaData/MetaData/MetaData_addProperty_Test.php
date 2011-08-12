<?php

use Orm\MetaData;

/**
 * @covers Orm\MetaData::addProperty
 */
class MetaData_addProperty_Test extends TestCase
{

	public function testOverwrite()
	{
		$m = new MetaData('MetaData_Test_Entity');

		$m->addProperty('id', 'int1', MetaData::READ);
		$m->addProperty('id', 'int2', MetaData::READ, 'Orm\Entity');
		$m->addProperty('id', 'int3', MetaData::READ, 'MetaData_Test_Entity');

		try {
			$m->addProperty('id', 'int4', MetaData::READ, 'MetaData_Test_Entity');
			throw new Exception;
		} catch (Nette\InvalidStateException $e) {}
		$this->assertInstanceOf('Nette\InvalidStateException', $e);
		$this->assertSame('MetaData_Test_Entity::$id is defined twice in MetaData_Test_Entity', $e->getMessage());

		try {
			$m->addProperty('id', 'int5', MetaData::READ);
			throw new Exception;
		} catch (Nette\InvalidStateException $e) {}
		$this->assertInstanceOf('Nette\InvalidStateException', $e);
		$this->assertSame('MetaData_Test_Entity::$id already defined (use param $since to redefine)', $e->getMessage());

		$array = $m->toArray();
		$this->assertSame(1, count($array));
		$this->assertArrayHasKey('id', $array);
		$this->assertSame(array('int3' => 'int3'), $array['id']['types']);

		$m->addProperty('id', 'int6', MetaData::READ, 'Neexistuje');

		$array = $m->toArray();
		$this->assertSame(array('int6' => 'int6'), $array['id']['types']);

	}

	public function testReturn()
	{
		$m = new MetaData('MetaData_Test_Entity');
		$property = $m->addProperty('id', 'int1', MetaData::READ);
		$this->assertInstanceof('Orm\MetaDataProperty', $property);
		$this->assertAttributeSame('id', 'name', $property);
		$this->assertAttributeSame($m->getEntityClass(), 'class', $property);
		$this->assertAttributeSame(array('id' => $property), 'properties', $m);
	}

}

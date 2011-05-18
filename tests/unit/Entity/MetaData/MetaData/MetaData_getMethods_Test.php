<?php

use Orm\MetaData;

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers MetaData::getMethods
 * @covers MetaDataProperty::setAccess
 */
class MetaData_getMethods_Test extends TestCase
{
	public function test()
	{
		$m = new MetaData('MetaData_Test_Entity');
		$m->addProperty('id', 'int', MetaData::READ, 'Entity');
		$this->assertEquals(
			array(
				'id' => array(
					'types' => array('int' => 'int'),
					'get' => array('method' => 'getId'),
					'set' => NULL,
					'since' => 'Entity',
					'relationship' => NULL,
					'relationshipParam' => NULL,
					'default' => NULL,
					'enum' => NULL,
					'injection' => NULL,
				)
			)
			, $m->toArray()
		);
	}


	public function testGetter()
	{
		$m = new MetaData('MetaData_Test_Entity');
		$m->addProperty('getter', 'NULL', MetaData::READ);
		$a = $m->toArray();
		$this->assertEquals('getGetter', $a['getter']['get']['method']);
		$this->assertEquals(NULL, $a['getter']['set']);
	}

	public function testSetter()
	{
		$m = new MetaData('MetaData_Test_Entity');
		$m->addProperty('setter', 'NULL', MetaData::READWRITE);
		$a = $m->toArray();
		$this->assertEquals(NULL, $a['setter']['get']['method']);
		$this->assertEquals('setSetter', $a['setter']['set']['method']);
	}

	public function testGetterSetter()
	{
		$m = new MetaData('MetaData_Test_Entity');
		$m->addProperty('getterSetter', 'NULL', MetaData::READWRITE);
		$a = $m->toArray();
		$this->assertEquals('getGetterSetter', $a['getterSetter']['get']['method']);
		$this->assertEquals('setGetterSetter', $a['getterSetter']['set']['method']);
	}

	public function testIs()
	{
		$m = new MetaData('MetaData_Test_Entity');
		$m->addProperty('getter', 'bool', MetaData::READWRITE);
		$a = $m->toArray();
		$this->assertEquals('isGetter', $a['getter']['get']['method']); // wtf existuje getGetter i isGetter, pouzije se posledni nastaveny
		$this->assertEquals(NULL, $a['getter']['set']['method']);

		$m->addProperty('getterSetter', 'bool', MetaData::READWRITE);
		$a = $m->toArray();
		$this->assertEquals('getGetterSetter', $a['getterSetter']['get']['method']);
		$this->assertEquals('setGetterSetter', $a['getterSetter']['set']['method']);

		$m = new MetaData('MetaData_Test_Entity');
		$m->addProperty('bool', 'bool', MetaData::READWRITE);
		$a = $m->toArray();
		$this->assertEquals('isBool', $a['bool']['get']['method']);
		$this->assertEquals(NULL, $a['bool']['set']['method']);

		$m = new MetaData('MetaData_Test_Entity');
		$m->addProperty('bool', 'bool|null', MetaData::READWRITE);
		$a = $m->toArray();
		$this->assertEquals(NULL, $a['bool']['get']['method']);
		$this->assertEquals(NULL, $a['bool']['set']['method']);
	}

}

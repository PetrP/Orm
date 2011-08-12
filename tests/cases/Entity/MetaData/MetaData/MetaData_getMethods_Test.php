<?php

use Orm\MetaData;

/**
 * @covers Orm\MetaData::getMethods
 * @covers Orm\MetaDataProperty::setAccess
 */
class MetaData_getMethods_Test extends TestCase
{
	public function test()
	{
		$m = new MetaData('MetaData_Test_Entity');
		$m->addProperty('id', 'int', MetaData::READ, 'Orm\Entity');
		$this->assertSame(
			array(
				'id' => array(
					'types' => array('int' => 'int'),
					'get' => array('method' => 'getId'),
					'set' => NULL,
					'since' => 'Orm\Entity',
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
		$this->assertSame('getGetter', $a['getter']['get']['method']);
		$this->assertSame(NULL, $a['getter']['set']);
	}

	public function testSetter()
	{
		$m = new MetaData('MetaData_Test_Entity');
		$m->addProperty('setter', 'NULL', MetaData::READWRITE);
		$a = $m->toArray();
		$this->assertSame(NULL, $a['setter']['get']['method']);
		$this->assertSame('setSetter', $a['setter']['set']['method']);
	}

	public function testGetterSetter()
	{
		$m = new MetaData('MetaData_Test_Entity');
		$m->addProperty('getterSetter', 'NULL', MetaData::READWRITE);
		$a = $m->toArray();
		$this->assertSame('getGetterSetter', $a['getterSetter']['get']['method']);
		$this->assertSame('setGetterSetter', $a['getterSetter']['set']['method']);
	}

	public function testIs()
	{
		$m = new MetaData('MetaData_Test_Entity');
		$m->addProperty('getter', 'bool', MetaData::READWRITE);
		$a = $m->toArray();
		$this->assertSame('isGetter', $a['getter']['get']['method']); // wtf existuje getGetter i isGetter, pouzije se posledni nastaveny
		$this->assertSame(NULL, $a['getter']['set']['method']);

		$m->addProperty('getterSetter', 'bool', MetaData::READWRITE);
		$a = $m->toArray();
		$this->assertSame('getGetterSetter', $a['getterSetter']['get']['method']);
		$this->assertSame('setGetterSetter', $a['getterSetter']['set']['method']);

		$m = new MetaData('MetaData_Test_Entity');
		$m->addProperty('bool', 'bool', MetaData::READWRITE);
		$a = $m->toArray();
		$this->assertSame('isBool', $a['bool']['get']['method']);
		$this->assertSame(NULL, $a['bool']['set']['method']);

		$m = new MetaData('MetaData_Test_Entity');
		$m->addProperty('bool', 'bool|null', MetaData::READWRITE);
		$a = $m->toArray();
		$this->assertSame(NULL, $a['bool']['get']['method']);
		$this->assertSame(NULL, $a['bool']['set']['method']);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\MetaData', 'getMethods');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

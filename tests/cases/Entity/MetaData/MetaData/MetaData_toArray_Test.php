<?php

use Orm\MetaData;

/**
 * @covers Orm\MetaData::toArray
 * @covers Orm\MetaDataProperty::toArray
 */
class MetaData_toArray_Test extends TestCase
{
	public function test()
	{
		$m = new MetaData('MetaData_Test_Entity');
		$m->addProperty('id', 'int', MetaData::READ, 'Orm\Entity');
		$m->addProperty('id2', 'string')->setDefault(false);
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
				),
				'id2' => array(
					'types' => array('string' => 'string'),
					'get' => array('method' => NULL),
					'set' => array('method' => NULL),
					'since' => NULL,
					'relationship' => NULL,
					'relationshipParam' => NULL,
					'default' => false,
					'enum' => NULL,
					'injection' => NULL,
				),
			)
			, $m->toArray()
		);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\MetaData', 'toArray');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

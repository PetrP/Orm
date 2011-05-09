<?php

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers MetaDataProperty::__construct
 * @covers MetaDataProperty::getSince
 */
class MetaDataProperty_construct_Test extends TestCase
{
	public function test()
	{
		$m = new MetaData('MetaData_Test_Entity');
		$property = new MetaDataProperty($m, 'id', 'types', MetaData::READWRITE, 'since');
		$this->assertEquals(
			array(
				'types' => array('types' => 'types'),
				'get' => array('method' => 'getId'),
				'set' => array('method' => NULL),
				'since' => 'since',
				'relationship' => NULL,
				'relationshipParam' => NULL,
				'default' => NULL,
				'enum' => NULL,
				'injection' => NULL,
			)
			, $a = $property->toArray()
		);
		$this->assertEquals('since', $property->getSince());
		$this->assertAttributeEquals('id', 'name', $property);
		$this->assertAttributeEquals($m->getEntityClass(), 'class', $property);
	}
}

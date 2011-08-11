<?php

/**
 * @covers Orm\BaseEntityFragment::createMetaData
 */
class BaseEntityFragment_createMetaData_Test extends TestCase
{

	public function test1()
	{
		$this->assertInstanceOf('Orm\MetaData', TestEntity::createMetaData('TestEntity'));
	}

	public function test2()
	{
		$this->assertSame(array(
			'id' => array(
				'types' => array('id' => 'id'),
				'get' => array('method' => 'getId'),
				'set' => NULL,
				'since' => 'Orm\Entity',
				'relationship' => NULL,
				'relationshipParam' => NULL,
				'default' => NULL,
				'enum' => NULL,
				'injection' => NULL,
			),
			'string' => array(
				'types' => array('string' => 'string'),
				'get' => array('method' => NULL),
				'set' => array('method' => NULL),
				'since' => 'TestEntity',
				'relationship' => NULL,
				'relationshipParam' => NULL,
				'default' => '',
				'enum' => NULL,
				'injection' => NULL,
			),
			'date' => array(
				'types' => array('datetime' => 'datetime'),
				'get' => array('method' => NULL),
				'set' => array('method' => NULL),
				'since' => 'TestEntity',
				'relationship' => NULL,
				'relationshipParam' => NULL,
				'default' => 'now',
				'enum' => NULL,
				'injection' => NULL,
			),
		), TestEntity::createMetaData('TestEntity')->toArray());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\BaseEntityFragment', 'createMetaData');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertTrue($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

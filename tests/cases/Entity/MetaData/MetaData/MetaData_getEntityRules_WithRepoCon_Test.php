<?php

use Nette\Utils\Html;
use Orm\MetaData;
use Orm\RepositoryContainer;

/**
 * @covers Orm\MetaData::getEntityRules
 * @covers Orm\MetaData::createEntityRules
 */
class MetaData_getEntityRules_WithRepoCon_Test extends TestCase
{
	private $m;
	protected function setUp()
	{
		MetaData::clean();
		MetaData_Test_Entity::$metaData = NULL;
		$this->m = new RepositoryContainer;
	}

	public function testCache()
	{
		MetaData_Test_Entity::$count = 0;
		MetaData::getEntityRules('MeTaData_Test_Entity', $this->m);
		MetaData::getEntityRules('MeTaData_Test_Entity', $this->m);
		MetaData::getEntityRules('MeTaData_Test_Entity', $this->m);
		$this->assertSame(1, MetaData_Test_Entity::$count);
	}

	public function testNotExists()
	{
		$this->setExpectedException('Nette\InvalidStateException', "Class 'Xxxasdsad' doesn`t exists");
		MetaData::getEntityRules('Xxxasdsad', $this->m);
	}

	public function testNotEntity()
	{
		$this->setExpectedException('Nette\InvalidStateException', "'Nette\\Utils\\Html' isn`t instance of Orm\\IEntity");
		MetaData::getEntityRules('Nette\Utils\Html', $this->m);
	}

	public function testBadReturn()
	{
		$this->setExpectedException('Orm\BadReturnException', "MetaData_Test_Entity::createMetaData() must return Orm\\MetaData, 'Nette\\Utils\\Html' given.");
		MetaData_Test_Entity::$metaData = new Html;
		MetaData::getEntityRules('MetaData_Test_Entity', $this->m);
	}

	public function testReturn()
	{
		$this->assertInternalType('array', MetaData::getEntityRules('MetaData_Test_Entity', $this->m));
	}

	public function testRecursionCache()
	{
		$this->assertAttributeEmpty('cache2', 'Orm\MetaData');
		MetaData::getEntityRules('RelationshipLoader_ManyToMany1_Entity', $this->m);
		$this->assertAttributeEmpty('cache2', 'Orm\MetaData');
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\MetaData', 'getEntityRules');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertTrue($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

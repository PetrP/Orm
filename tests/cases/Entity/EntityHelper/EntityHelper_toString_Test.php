<?php

use Orm\EntityHelper;
use Orm\RepositoryContainer;
use Orm\MetaData;

/**
 * @covers Orm\EntityHelper::toString
 */
class EntityHelper_toString_Test extends TestCase
{

	public function testNotPersisted()
	{
		$e = new TestEntity;
		$this->assertSame('TestEntity', EntityHelper::toString($e));
	}

	public function testPersisted()
	{
		$m = new RepositoryContainer;
		$e1 = $m->tests->getById(1);
		$e2 = $m->tests->getById(2);
		$this->assertSame('TestEntity#1', EntityHelper::toString($e1));
		$this->assertSame('TestEntity#2', EntityHelper::toString($e2));
	}

	public function testNoId()
	{
		MetaData_Test2_Entity::$metaData = new MetaData('MetaData_Test2_Entity');
		$e = new MetaData_Test2_Entity;
		MetaData_Test2_Entity::$metaData = NULL;
		$this->assertSame('MetaData_Test2_Entity', EntityHelper::toString($e));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\EntityHelper', 'toString');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertTrue($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

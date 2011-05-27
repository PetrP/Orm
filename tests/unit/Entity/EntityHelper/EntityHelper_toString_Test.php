<?php

use Orm\EntityHelper;
use Orm\RepositoryContainer;
use Orm\MetaData;

require_once dirname(__FILE__) . '/../../../boot.php';

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

}

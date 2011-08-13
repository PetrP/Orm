<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\ArrayMapper::saveData
 */
class ArrayMapper_saveData_Test extends TestCase
{
	private $m;
	protected function setUp()
	{
		$this->m = new ArrayMapper_saveData_ArrayMapper(new TestsRepository(new RepositoryContainer));
	}

	public function testReturn()
	{
		$this->setExpectedException('Orm\NotImplementedException', 'ArrayMapper_saveData_ArrayMapper::saveData() is not implement, you must override and implement that method');
		$this->m->persist(new TestEntity);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ArrayMapper', 'saveData');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

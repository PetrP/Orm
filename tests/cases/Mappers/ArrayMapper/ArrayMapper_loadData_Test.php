<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\ArrayMapper::loadData
 */
class ArrayMapper_loadData_Test extends TestCase
{
	private $m;
	protected function setUp()
	{
		$this->m = new ArrayMapper_loadData_ArrayMapper(new TestsRepository(new RepositoryContainer));
	}

	public function testReturn()
	{
		$this->setExpectedException('Orm\NotImplementedException', 'ArrayMapper_loadData_ArrayMapper::loadData() is not implement, you must override and implement that method');
		$this->m->findAll();
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ArrayMapper', 'loadData');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

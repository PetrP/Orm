<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\ArrayMapper::unlock
 */
class ArrayMapper_unlock_Test extends TestCase
{
	private $m1;
	private $m2;
	protected function setUp()
	{
		$this->m1 = new ArrayMapper_lock_ArrayMapper(new TestsRepository(new RepositoryContainer));
		$this->m2 = new ArrayMapper_lock_ArrayMapper(new TestsRepository(new RepositoryContainer));
	}

	public function test()
	{
		$this->m1->_lock();
		$this->m1->_unlock();
		$this->m2->_lock();
		$this->m2->_unlock();
		$this->m1->_lock();
		$this->m1->_unlock();
		$this->assertTrue(true);
	}

	public function testNotEntered()
	{
		$this->setExpectedException('Orm\ArrayMapperLockException', 'Critical section has not been initialized.');
		$this->m1->_unlock();
	}

	public function testDiferentMapper()
	{
		$this->m1->_lock();
		$this->m2->_unlock();
		$this->assertTrue(true);
	}

	public function testNotEntered2()
	{
		$this->m1->_lock();
		$this->m1->_unlock();
		$this->setExpectedException('Orm\ArrayMapperLockException', 'Critical section has not been initialized.');
		$this->m1->_unlock();
	}

	public function testNotEnteredDiferentMapper()
	{
		$this->m1->_lock();
		$this->m1->_unlock();
		$this->setExpectedException('Orm\ArrayMapperLockException', 'Critical section has not been initialized.');
		$this->m2->_unlock();
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ArrayMapper', 'unlock');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

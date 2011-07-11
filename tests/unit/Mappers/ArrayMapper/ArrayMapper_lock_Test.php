<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\ArrayMapper::lock
 */
class ArrayMapper_lock_Test extends TestCase
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

	public function testEntered()
	{
		$this->m1->_lock();
		$this->setExpectedException('Nette\InvalidStateException', 'Critical section has already been entered.');
		try {
			$this->m1->_lock();
		} catch (Exception $e) {
			$this->m1->_unlock();
			throw $e;
		}
	}

	public function testEnteredDiferentMapper()
	{
		$this->m1->_lock();
		$this->setExpectedException('Nette\InvalidStateException', 'Critical section has already been entered.');
		try {
			$this->m2->_lock();
		} catch (Exception $e) {
			$this->m1->_unlock();
			throw $e;
		}
	}

}

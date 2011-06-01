<?php

use Orm\DibiManyToManyMapper;

require_once __DIR__ . '/../../../boot.php';

/**
 * @covers Orm\DibiManyToManyMapper::setParams
 */
class DibiManyToManyMapper_setParams_Test extends TestCase
{
	private $mm;

	protected function setUp()
	{
		$c = new DibiConnection(array('lazy' => true));
		$this->mm = new DibiManyToManyMapper($c);
	}

	public function test()
	{
		$this->mm->firstParam = 'x';
		$this->mm->secondParam = 'y';
		$this->mm->table = 't';
		$this->mm->setParams(false);
		$this->assertAttributeSame(false, 'parentIsFirst', $this->mm);
		$this->mm->setParams(true);
		$this->assertAttributeSame(true, 'parentIsFirst', $this->mm);
		$this->mm->setParams(0);
		$this->assertAttributeSame(false, 'parentIsFirst', $this->mm);
		$this->mm->setParams('xyz');
		$this->assertAttributeSame(true, 'parentIsFirst', $this->mm);
	}

	public function testNoFirstParam()
	{
		$this->mm->secondParam = 'y';
		$this->mm->table = 't';
		$this->setExpectedException('Nette\InvalidStateException', 'Orm\DibiManyToManyMapper::$firstParam is required');
		$this->mm->setParams(false);
	}

	public function testNoSecondParam()
	{
		$this->mm->firstParam = 'x';
		$this->mm->table = 't';
		$this->setExpectedException('Nette\InvalidStateException', 'Orm\DibiManyToManyMapper::$secondParam is required');
		$this->mm->setParams(false);
	}

	public function testNoTable()
	{
		$this->mm->firstParam = 'x';
		$this->mm->secondParam = 'y';
		$this->setExpectedException('Nette\InvalidStateException', 'Orm\DibiManyToManyMapper::$table is required');
		$this->mm->setParams(false);
	}
}

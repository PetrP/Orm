<?php

use Orm\DibiManyToManyMapper;

/**
 * @covers Orm\DibiManyToManyMapper::setParams
 * @covers Orm\DibiManyToManyMapper::getFirstParam
 * @covers Orm\DibiManyToManyMapper::setFirstParam
 * @covers Orm\DibiManyToManyMapper::getSecondParam
 * @covers Orm\DibiManyToManyMapper::setSecondParam
 */
class DibiManyToManyMapper_deprecated_Test extends TestCase
{
	private $mm;

	protected function setUp()
	{
		$c = new DibiConnection(array('lazy' => true));
		$this->mm = new DibiManyToManyMapper($c);
	}

	public function testSetParams()
	{
		$this->setExpectedException('Nette\DeprecatedException');
		$this->mm->setParams(false);
	}

	public function testFirstParamSet()
	{
		$this->setExpectedException('Nette\DeprecatedException', 'Orm\DibiManyToManyMapper::$firstParam is deprecated; use Orm\DibiManyToManyMapper::$childParam instead');
		$this->mm->firstParam = 'foo';
	}

	public function testFirstParamGet()
	{
		$this->setExpectedException('Nette\DeprecatedException', 'Orm\DibiManyToManyMapper::$firstParam is deprecated; use Orm\DibiManyToManyMapper::$childParam instead');
		$this->mm->firstParam;
	}

	public function testSecondParamSet()
	{
		$this->setExpectedException('Nette\DeprecatedException', 'Orm\DibiManyToManyMapper::$secondParam is deprecated; use Orm\DibiManyToManyMapper::$parentParam instead');
		$this->mm->secondParam = 'foo';
	}

	public function testSecondParamGet()
	{
		$this->setExpectedException('Nette\DeprecatedException', 'Orm\DibiManyToManyMapper::$secondParam is deprecated; use Orm\DibiManyToManyMapper::$parentParam instead');
		$this->mm->secondParam;
	}

}

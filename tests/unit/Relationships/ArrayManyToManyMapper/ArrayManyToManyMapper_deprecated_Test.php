<?php

use Orm\ArrayManyToManyMapper;

require_once __DIR__ . '/../../../boot.php';

/**
 * @covers Orm\ArrayManyToManyMapper::setParams
 * @covers Orm\ArrayManyToManyMapper::setValue
 * @covers Orm\ArrayManyToManyMapper::getValue
 */
class ArrayManyToManyMapper_deprecated_Test extends TestCase
{
	private $mm;

	protected function setUp()
	{
		$this->mm = new ArrayManyToManyMapper;
	}

	public function testSetParams()
	{
		$this->setExpectedException('Nette\DeprecatedException');
		$this->mm->setParams(false);
	}

	public function testSetValue()
	{
		$this->setExpectedException('Nette\DeprecatedException', 'Orm\ArrayManyToManyMapper::setValue() is deprecated; use Orm\ArrayManyToManyMapper::setInjectedValue() instead');
		$this->mm->setValue(array());
	}

	public function testGetValue()
	{
		$this->setExpectedException('Nette\DeprecatedException', 'Orm\ArrayManyToManyMapper::getValue() is deprecated; use Orm\ArrayManyToManyMapper::getInjectedValue() instead');
		$this->mm->getValue();
	}

}

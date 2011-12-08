<?php

use Orm\ArrayManyToManyMapper;

/**
 * @covers Orm\ArrayManyToManyMapper::setParams
 * @covers Orm\ArrayManyToManyMapper::setValue
 * @covers Orm\ArrayManyToManyMapper::getValue
 * @covers Orm\ArrayManyToManyMapper::getInjectedValue
 * @covers Orm\ArrayManyToManyMapper::setInjectedValue
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
		$this->setExpectedException('Orm\DeprecatedException', 'Orm\ArrayManyToManyMapper::setParams() is deprecated; use Orm\ArrayManyToManyMapper::attach() instead');
		$this->mm->setParams(false);
	}

	public function testSetValue()
	{
		$this->setExpectedException('Orm\DeprecatedException', 'Orm\ArrayManyToManyMapper::setValue() is deprecated; use Orm\ArrayManyToManyMapper::setInjectedValue() instead');
		$this->mm->setValue(array());
	}

	public function testGetValue()
	{
		$this->setExpectedException('Orm\DeprecatedException', 'Orm\ArrayManyToManyMapper::getValue() is deprecated; use Orm\ArrayManyToManyMapper::getInjectedValue() instead');
		$this->mm->getValue();
	}

	public function testGetInjectedValue()
	{
		$this->setExpectedException('Orm\DeprecatedException', 'Orm\ArrayManyToManyMapper::getInjectedValue() is deprecated; use Orm\ArrayManyToManyMapper::validateInjectedValue() instead');
		$this->mm->getInjectedValue();
	}

	public function testSetInjectedValue()
	{
		$this->setExpectedException('Orm\DeprecatedException', 'Orm\ArrayManyToManyMapper::setInjectedValue() is deprecated; use Orm\ArrayManyToManyMapper::validateInjectedValue() instead');
		$this->mm->setInjectedValue(NULL);
	}
}

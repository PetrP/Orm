<?php

use Orm\ArrayManyToManyMapper;

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

}

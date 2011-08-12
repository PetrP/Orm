<?php

use Orm\ArrayManyToManyMapper;

/**
 * @covers Orm\ArrayManyToManyMapper::setInjectedValue
 * @covers Orm\ArrayManyToManyMapper::getInjectedValue
 */
class ArrayManyToManyMapper_InjectedValue_Test extends TestCase
{
	private $mm;

	protected function setUp()
	{
		$this->mm = new ArrayManyToManyMapper;
	}

	public function test1()
	{
		$this->mm->setInjectedValue(array(1, 2));
		$this->assertSame(array(1=>1, 2=>2), $this->mm->getInjectedValue());
	}

	public function test2()
	{
		$this->mm->setInjectedValue(array('abc' => 53, 'bcd' => 2));
		$this->assertSame(array(53=>53, 2=>2), $this->mm->getInjectedValue());
	}

	public function testEmpty1()
	{
		$this->mm->setInjectedValue(array());
		$this->assertSame(array(), $this->mm->getInjectedValue());
	}

	public function testEmpty2()
	{
		$this->mm->setInjectedValue(NULL);
		$this->assertSame(array(), $this->mm->getInjectedValue());
	}

	public function testNotValid()
	{
		$this->mm->setInjectedValue('foobar');
		$this->assertSame(array(), $this->mm->getInjectedValue());
	}

	public function testSerialized()
	{
		$this->mm->setInjectedValue(serialize(array(9,8,7)));
		$this->assertSame(array(9=>9,8=>8,7=>7), $this->mm->getInjectedValue());
	}

	public function testSerializedEmpty()
	{
		$this->mm->setInjectedValue(serialize(array()));
		$this->assertSame(array(), $this->mm->getInjectedValue());
	}
}

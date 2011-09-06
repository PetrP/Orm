<?php

use Orm\ArrayManyToManyMapper;
use Orm\ManyToMany;

/**
 * @covers Orm\ArrayManyToManyMapper::attach
 */
class ArrayManyToManyMapper_attach_Test extends TestCase
{
	private $mm;

	protected function setUp()
	{
		$this->mm = new ArrayManyToManyMapper;
	}

	public function testMapped()
	{
		$this->mm->attach(new ManyToMany(new TestEntity, 'foo', 'foo', 'foo', true));
		$this->assertTrue(true);
	}

	public function testNotMapped()
	{
		$this->setExpectedException('Orm\NotSupportedException', 'Orm\ArrayManyToManyMapper has support only on side where is realtionship mapped.');
		$this->mm->attach(new ManyToMany(new TestEntity, 'foo', 'foo', 'foo', false));
	}

}

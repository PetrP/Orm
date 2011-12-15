<?php

use Orm\ArrayManyToManyMapper;
use Orm\RelationshipMetaDataManyToMany;

/**
 * @covers Orm\ArrayManyToManyMapper::validateInjectedValue
 */
class ArrayManyToManyMapper_validateInjectedValue_Test extends TestCase
{
	private $mm;

	protected function setUp()
	{
		$this->mm = new ArrayManyToManyMapper;
		$this->mm->attach(new RelationshipMetaDataManyToMany('TestEntity', 'id', 'tests', NULL));
	}

	public function test1()
	{
		$this->assertSame(array(1=>1, 2=>2), $this->mm->validateInjectedValue(array(1, 2)));
	}

	public function test2()
	{
		$this->assertSame(array(53=>53, 2=>2), $this->mm->validateInjectedValue(array('abc' => 53, 'bcd' => 2)));
	}

	public function testEmpty1()
	{
		$this->assertSame(array(), $this->mm->validateInjectedValue(array()));
	}

	public function testEmpty2()
	{
		$this->assertSame(array(), $this->mm->validateInjectedValue(NULL));
	}

	public function testNotValid()
	{
		$this->assertSame(array(), $this->mm->validateInjectedValue('foobar'));
	}

	public function testSerialized()
	{
		$this->assertSame(array(9=>9,8=>8,7=>7), $this->mm->validateInjectedValue(serialize(array(9,8,7))));
	}

	public function testSerializedEmpty()
	{
		$this->assertSame(array(), $this->mm->validateInjectedValue(serialize(array())));
	}

	public function testMappedThere()
	{
		$this->mm = new ArrayManyToManyMapper;
		$this->mm->attach(new RelationshipMetaDataManyToMany('TestEntity', 'id', 'tests', 'foo', NULL, RelationshipMetaDataManyToMany::MAPPED_THERE));
		$this->assertSame(NULL, $this->mm->validateInjectedValue(array(1, 2)));
	}
}

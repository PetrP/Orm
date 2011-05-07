<?php

require_once __DIR__ . '/../../../../boot.php';

/**
 * @covers MetaDataProperty::setTypes
 */
class MetaDataProperty_setTypes_Test extends TestCase
{
	private $mixed = array('mixed' => 'mixed', 'null' => 'null');

	private function setTypes($types)
	{
		$m = new MetaData('MetaData_Test_Entity');
		$property = new MetaDataProperty($m, 'id', $types);
		$a = $property->toArray();
		return $a['types'];
	}

	public function testOne()
	{
		$this->assertEquals(array('blabla' => 'blabla'), $this->setTypes('BlaBla'));
	}

	public function testMore()
	{
		$this->assertEquals(array(
		'blabla' => 'blabla',
		'int' => 'int',
		'bool' => 'bool',
		'null' => 'null',
		), $this->setTypes('BlaBla|int|BOOL|NULL'));
	}

	public function testArray()
	{
		$this->assertEquals(array(
		'bool' => 'bool',
		'string' => 'string',
		), $this->setTypes(array('boOL', 'STRinG')));
	}

	public function testMixed()
	{
		$this->assertEquals($this->mixed, $this->setTypes('mixed'));
		$this->assertEquals($this->mixed, $this->setTypes('BlaBla|mIXed|bool'));
		$this->assertEquals($this->mixed, $this->setTypes('mixed|NULL'));
	}

	public function testAllias()
	{
		$this->assertEquals(array('null' => 'null'), $this->setTypes('void'));
		$this->assertEquals(array('float' => 'float'), $this->setTypes('double'));
		$this->assertEquals(array('float' => 'float'), $this->setTypes('real'));
		$this->assertEquals(array('float' => 'float'), $this->setTypes('numeric'));
		$this->assertEquals(array('float' => 'float'), $this->setTypes('number'));
		$this->assertEquals(array('int' => 'int'), $this->setTypes('integer'));
		$this->assertEquals(array('bool' => 'bool'), $this->setTypes('boolean'));
		$this->assertEquals(array('string' => 'string'), $this->setTypes('text'));

		$this->assertEquals(array('scalar' => 'scalar'), $this->setTypes('scalar')); // todo
	}

	public function testMultiple()
	{
		$this->assertEquals(array('int' => 'int'), $this->setTypes('int|INT|int'));
		$this->assertEquals(array('float' => 'float'), $this->setTypes('float|double|real|numeric|number'));
		$this->assertEquals(array('float' => 'float', 'int' => 'int'), $this->setTypes('float|double|real|numeric|number|integer|int'));
		$this->assertEquals(array('float' => 'float', 'datetime' => 'datetime'), $this->setTypes(array('float', 'real', 'dateTime', 'DateTime')));
	}

	public function testEmpty()
	{
		$this->assertEquals($this->mixed, $this->setTypes(array()));
		$this->assertEquals($this->mixed, $this->setTypes(''));
	}

	public function testTrim()
	{
		$this->assertEquals(array('int' => 'int', 'float' => 'float'), $this->setTypes(array('    int', ' float ')));
		$this->assertEquals($this->mixed, $this->setTypes('    '));
		$this->assertEquals($this->mixed, $this->setTypes(array('    ', '  ')));
		$this->assertEquals(array('int' => 'int'), $this->setTypes('    |int|  '));
		$this->assertEquals(array('int' => 'int', 'bool' => 'bool'), $this->setTypes('int | bool |'));
	}

	/** Asociace se pta jestli je null, a mixed muze byt null */
	public function testMixedContainsNull_ManyToOne()
	{
		new Model;
		$m = new MetaData('MetaData_Test2_Entity');
		$m->addProperty('fk', 'mixed')
			->setManyToOne('MetaData_Test2')
		;
		$m->addProperty('enum', 'mixed')
			->setEnum(array('a','b','c'))
		;

		MetaData_Test2_Entity::$metaData = $m;
		$e = new MetaData_Test2_Entity;
		MetaData_Test2_Entity::$metaData = NULL;

		$e->fk = NULL;
		$this->assertSame(NULL, $e->fk);

		$e->fk = 'abc';
		$this->assertSame(NULL, $e->fk);
	}

	/** Enum se pta jestli je null, a mixed muze byt null */
	public function testMixedContainsNull_Enum()
	{
		$e = new MetaData_Test2_Entity;

		$e->enum = NULL;
		$this->assertSame(NULL, $e->enum);

		$e->enum = 'a';
		$this->assertSame('a', $e->enum);

		$this->setExpectedException('UnexpectedValueException', "Param MetaData_Test2_Entity::\$enum must be 'a, b, c', 'd' given");
		$e->enum = 'd';
	}
}

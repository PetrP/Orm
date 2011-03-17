<?php

require_once __DIR__ . '/../../../../boot.php';

class MetaDataProperty_setTypes_Test extends TestCase
{
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
		$this->assertEquals(array(), $this->setTypes('mixed'));
		$this->assertEquals(array(), $this->setTypes('BlaBla|mIXed|bool'));
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
		$this->assertEquals(array(), $this->setTypes(array()));
		$this->assertEquals(array(), $this->setTypes(''));
	}

	public function testTrim()
	{
		$this->assertEquals(array('int' => 'int', 'float' => 'float'), $this->setTypes(array('    int', ' float ')));
		$this->assertEquals(array(), $this->setTypes('    '));
		$this->assertEquals(array(), $this->setTypes(array('    ', '  ')));
		$this->assertEquals(array('int' => 'int'), $this->setTypes('    |int|  '));
		$this->assertEquals(array('int' => 'int', 'bool' => 'bool'), $this->setTypes('int | bool |'));
	}

}

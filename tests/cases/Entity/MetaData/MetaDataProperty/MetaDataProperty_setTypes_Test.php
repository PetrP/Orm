<?php

use Orm\MetaData;
use Orm\MetaDataProperty;
use Orm\RepositoryContainer;

/**
 * @covers Orm\MetaDataProperty::setTypes
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
		$this->assertSame(array('blabla' => 'blabla'), $this->setTypes('BlaBla'));
	}

	public function testMore()
	{
		$this->assertSame(array(
		'blabla' => 'blabla',
		'int' => 'int',
		'bool' => 'bool',
		'null' => 'null',
		), $this->setTypes('BlaBla|int|BOOL|NULL'));
	}

	public function testArray()
	{
		$this->assertSame(array(
		'bool' => 'bool',
		'string' => 'string',
		), $this->setTypes(array('boOL', 'STRinG')));
	}

	public function testMixed()
	{
		$this->assertSame($this->mixed, $this->setTypes('mixed'));
		$this->assertSame($this->mixed, $this->setTypes('BlaBla|mIXed|bool'));
		$this->assertSame($this->mixed, $this->setTypes('mixed|NULL'));
	}

	public function testAllias()
	{
		$this->assertSame(array('null' => 'null'), $this->setTypes('void'));
		$this->assertSame(array('float' => 'float'), $this->setTypes('double'));
		$this->assertSame(array('float' => 'float'), $this->setTypes('real'));
		$this->assertSame(array('float' => 'float'), $this->setTypes('numeric'));
		$this->assertSame(array('float' => 'float'), $this->setTypes('number'));
		$this->assertSame(array('int' => 'int'), $this->setTypes('integer'));
		$this->assertSame(array('bool' => 'bool'), $this->setTypes('boolean'));
		$this->assertSame(array('string' => 'string'), $this->setTypes('text'));

		$this->assertSame(array('scalar' => 'scalar'), $this->setTypes('scalar')); // todo
	}

	public function testMultiple()
	{
		$this->assertSame(array('int' => 'int'), $this->setTypes('int|INT|int'));
		$this->assertSame(array('float' => 'float'), $this->setTypes('float|double|real|numeric|number'));
		$this->assertSame(array('float' => 'float', 'int' => 'int'), $this->setTypes('float|double|real|numeric|number|integer|int'));
		$this->assertSame(array('float' => 'float', 'datetime' => 'datetime'), $this->setTypes(array('float', 'real', 'dateTime', 'DateTime')));
	}

	public function testEmpty()
	{
		$this->assertSame($this->mixed, $this->setTypes(array()));
		$this->assertSame($this->mixed, $this->setTypes(''));
	}

	public function testTrim()
	{
		$this->assertSame(array('int' => 'int', 'float' => 'float'), $this->setTypes(array('    int', ' float ')));
		$this->assertSame($this->mixed, $this->setTypes('    '));
		$this->assertSame($this->mixed, $this->setTypes(array('    ', '  ')));
		$this->assertSame(array('int' => 'int'), $this->setTypes('    |int|  '));
		$this->assertSame(array('int' => 'int'), $this->setTypes('int|'));
		$this->assertSame(array('int' => 'int'), $this->setTypes('|int'));
		$this->assertSame(array('string' => 'string', 'int' => 'int'), $this->setTypes('string||int'));
		$this->assertSame(array('string' => 'string', 'int' => 'int'), $this->setTypes('string| | |int'));
		$this->assertSame(array('int' => 'int', 'bool' => 'bool'), $this->setTypes('int | bool |'));
	}

	/** Asociace se pta jestli je null, a mixed muze byt null */
	public function testMixedContainsNull_ManyToOne()
	{
		$m = new MetaData('MetaData_Test2_Entity');
		$m->addProperty('fk', 'mixed')
			->setManyToOne('MetaData_Test2')
		;
		$m->addProperty('enum', 'mixed')
			->setEnum(array('a','b','c'))
		;

		MetaData_Test2_Entity::$metaData = $m;
		MetaData::clean();
		$e = new MetaData_Test2_Entity;
		MetaData_Test2_Entity::$metaData = NULL;
		$model = new RepositoryContainer;
		$e->fireEvent('onAttach', $model->tests);

		$e->fk = NULL;
		$this->assertSame(NULL, $e->fk);

		$e->fk = 'abc';
		$this->assertSame(NULL, $e->fk);
	}

	/** Enum se pta jestli je null, a mixed muze byt null */
	public function testMixedContainsNull_Enum()
	{
		$m = new MetaData('MetaData_Test2_Entity');
		$m->addProperty('fk', 'mixed')
			->setManyToOne('MetaData_Test2')
		;
		$m->addProperty('enum', 'mixed')
			->setEnum(array('a','b','c'))
		;

		MetaData_Test2_Entity::$metaData = $m;
		MetaData::clean();
		$e = new MetaData_Test2_Entity;
		MetaData_Test2_Entity::$metaData = NULL;

		$e->enum = NULL;
		$this->assertSame(NULL, $e->enum);

		$e->enum = 'a';
		$this->assertSame('a', $e->enum);

		$this->setExpectedException('Orm\NotValidException', "Param MetaData_Test2_Entity::\$enum must be 'a', 'b', 'c'; 'd' given");
		$e->enum = 'd';
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\MetaDataProperty', 'setTypes');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

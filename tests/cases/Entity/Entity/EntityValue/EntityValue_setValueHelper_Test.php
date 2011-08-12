<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\_EntityValue::setValueHelper
 * @see EntityValue_getter_Test
 * @see EntityValue_injection_Test
 */
class EntityValue_setValueHelper_Test extends TestCase
{
	private $e;
	private $r;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->e = new EntityValue_getset_Entity;
		$this->r = $m->TestEntity;
		$this->e->___event($this->e, 'attach', $this->r);
	}

	public function testFkOk()
	{
		$fke = $this->r->getById(1);
		$this->e->fk = $fke->id;
		$this->assertSame($fke->id, $this->e->fk->id);
	}

	public function testFkNull()
	{
		$this->setExpectedException('UnexpectedValueException', "Param EntityValue_getset_Entity::\$fk must be 'testentity', 'NULL' given");
		$this->e->fk;
	}

	public function testFkBadValue()
	{
		$this->setExpectedException('UnexpectedValueException', "Entity(testentity) 'xxx' not found in `TestEntityRepository` in EntityValue_getset_Entity::\$fk");
		$this->e->fk = 'xxx';
	}

	public function testFkBadValue2()
	{
		$this->setExpectedException('UnexpectedValueException', "Entity(testentity) '0' not found in `TestEntityRepository` in EntityValue_getset_Entity::\$fk");
		$this->e->fk = 0;
	}

	public function testFkBadValue3()
	{
		$this->setExpectedException('UnexpectedValueException', "Entity(testentity) '0' not found in `TestEntityRepository` in EntityValue_getset_Entity::\$fk");
		$this->e->fk = '0';
	}

	public function testFkBadValue4()
	{
		$this->setExpectedException('UnexpectedValueException', "Entity(testentity) '' not found in `TestEntityRepository` in EntityValue_getset_Entity::\$fk");
		$this->e->fk = '';
	}

	public function testFkOrNull()
	{
		$this->e->fk2 = 'xxx';
		$this->assertSame(NULL, $this->e->fk2);
		$this->e->fk2 = NULL;
		$this->assertSame(NULL, $this->e->fk2);
	}

	public function testEnumString()
	{
		$e = new EntityValue_setValueHelper_Entity;
		$e->enumString = 'a';
		$this->assertSame('a', $e->enumString);
		$e->enumString = 'b';
		$this->assertSame('b', $e->enumString);
	}

	public function testEnumStringBad1()
	{
		$e = new EntityValue_setValueHelper_Entity;
		$this->setExpectedException('Nette\UnexpectedValueException', "Param EntityValue_setValueHelper_Entity::\$enumString must be ''a', 'b'', 'c' given");
		$e->enumString = 'c';
	}

	public function testEnumStringBad2()
	{
		$e = new EntityValue_setValueHelper_Entity;
		$this->setExpectedException('Nette\UnexpectedValueException', "Param EntityValue_setValueHelper_Entity::\$enumString must be ''a', 'b'', 'NULL' given");
		$e->enumString = NULL;
	}

	public function testEnumInt()
	{
		$e = new EntityValue_setValueHelper_Entity;
		$e->enumInt = 1;
		$this->assertSame(1, $e->enumInt);
		$e->enumInt = 2;
		$this->assertSame(2, $e->enumInt);
		$e->enumInt = '1';
		$this->assertSame(1, $e->enumInt);
		$e->enumInt = '2';
		$this->assertSame(2, $e->enumInt);
	}

	public function testEnumIntBad1()
	{
		$e = new EntityValue_setValueHelper_Entity;
		$this->setExpectedException('Nette\UnexpectedValueException', "Param EntityValue_setValueHelper_Entity::\$enumInt must be '1, 2', '3' given");
		$e->enumInt = 3;
	}

	public function testEnumIntBad2()
	{
		$e = new EntityValue_setValueHelper_Entity;
		$this->setExpectedException('Nette\UnexpectedValueException', "Param EntityValue_setValueHelper_Entity::\$enumInt must be '1, 2', 'c' given");
		$e->enumInt = 'c';
	}

	public function testEnumIntBad3()
	{
		$e = new EntityValue_setValueHelper_Entity;
		$this->setExpectedException('Nette\UnexpectedValueException', "Param EntityValue_setValueHelper_Entity::\$enumInt must be '1, 2', 'NULL' given");
		$e->enumInt = NULL;
	}

	public function testEnumIntZero()
	{
		$e = new EntityValue_setValueHelper_Entity;
		$e->enumIntZero = 0;
		$this->assertSame(0, $e->enumIntZero);
		$e->enumIntZero = 'c';
		$this->assertSame(0, $e->enumIntZero); // bug?
		$e->enumIntZero = NULL;
		$this->assertSame(0, $e->enumIntZero); // bug?
	}

	public function testEnumBool()
	{
		$e = new EntityValue_setValueHelper_Entity;
		$e->enumBool = true;
		$this->assertSame(true, $e->enumBool);
		$e->enumBool = false;
		$this->assertSame(false, $e->enumBool);
		$e->enumBool = 123;
		$this->assertSame(true, $e->enumBool);
		$e->enumBool = NULL;
		$this->assertSame(false, $e->enumBool);
		$e->enumBool = 'asdasd';
		$this->assertSame(true, $e->enumBool);
	}

	public function testEnumNull()
	{
		$e = new EntityValue_setValueHelper_Entity;
		$e->enumNull = NULL;
		$this->assertSame(NULL, $e->enumNull);
	}

}

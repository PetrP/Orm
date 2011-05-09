<?php

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers _EntityBase::offsetExists
 * @covers _EntityBase::offsetGet
 * @covers _EntityBase::offsetSet
 * @covers _EntityBase::offsetUnset
 */
class EntityBase_ArrayAccess_Test extends TestCase
{
	private $e;
	protected function setUp()
	{
		$this->e = new EntityBase_ArrayAccess_Entity;
	}

	public function testOffsetExists()
	{
		$this->e->string = 'ok';
		$this->assertSame(true, isset($this->e['string']));
	}

	public function testOffsetExists_null()
	{
		$this->e->string = NULL;
		$this->assertSame(false, isset($this->e['string']));
	}

	public function testOffsetExists_unexists()
	{
		$this->assertSame(false, isset($this->e['unexists']));
	}

	public function testOffsetGet()
	{
		$this->e->string = 'ok';
		$this->assertSame('ok', $this->e['string']);
	}

	public function testOffsetGet_null()
	{
		$this->e->string = NULL;
		$this->assertSame(NULL, $this->e['string']);
	}

	public function testOffsetGet_unexists()
	{
		$this->setExpectedException('MemberAccessException', 'Cannot read an undeclared property EntityBase_ArrayAccess_Entity::$unexists.');
		$this->e['unexists'];
	}

	public function testOffsetSet()
	{
		$this->e['string'] = 'ok';
		$this->assertSame('ok', $this->e->string);
	}

	public function testOffsetSet_null()
	{
		$this->e['string'] = NULL;
		$this->assertSame(NULL, $this->e->string);
	}

	public function testOffsetSet_unexists()
	{
		$this->setExpectedException('MemberAccessException', 'Cannot write to an undeclared property EntityBase_ArrayAccess_Entity::$unexists.');
		$this->e['unexists'] = 'xyz';
	}

	public function testOffsetUnset()
	{
		$this->setExpectedException('NotSupportedException');
		unset($this->e['string']);
	}
}

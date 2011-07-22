<?php

/**
 * @covers Orm\ValueEntityFragment::setValue
 * @see ValueEntityFragment_setter_Test
 */
class ValueEntityFragment_setValue_Test extends TestCase
{
	private $e;

	protected function setUp()
	{
		$this->e = new ValueEntityFragment_getset_Entity;
	}

	public function testUnexists()
	{
		$this->setExpectedException('Nette\MemberAccessException', 'Cannot write to an undeclared property ValueEntityFragment_getset_Entity::$unexists.');
		$this->e->sv('unexists', 123);
	}

	public function test()
	{
		$this->assertSame($this->e, $this->e->sv('string', 'xyz'));
		$this->assertSame('xyz', $this->e->string);
	}

	public function testReadOnly()
	{
		$this->setExpectedException('Nette\MemberAccessException', 'Cannot write to a read-only property ValueEntityFragment_getset_Entity::$readOnly.');
		$this->e->sv('readOnly', 'xyz');
	}
}

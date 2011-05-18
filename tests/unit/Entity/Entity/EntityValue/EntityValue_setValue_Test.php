<?php

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers Orm\_EntityValue::setValue
 * @see EntityValue_setter_Test
 */
class EntityValue_setValue_Test extends TestCase
{
	private $e;

	protected function setUp()
	{
		$this->e = new EntityValue_getset_Entity;
	}

	public function testUnexists()
	{
		$this->setExpectedException('Nette\MemberAccessException', 'Cannot write to an undeclared property EntityValue_getset_Entity::$unexists.');
		$this->e->sv('unexists', 123);
	}

	public function test()
	{
		$this->assertSame($this->e, $this->e->sv('string', 'xyz'));
		$this->assertSame('xyz', $this->e->string);
	}

	public function testReadOnly()
	{
		$this->setExpectedException('Nette\MemberAccessException', 'Cannot write to a read-only property EntityValue_getset_Entity::$readOnly.');
		$this->e->sv('readOnly', 'xyz');
	}
}

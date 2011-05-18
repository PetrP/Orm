<?php

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers _EntityValue::__set
 * @see EntityValue_setter_Test
 */
class EntityValue_set_Test extends TestCase
{
	private $e;

	protected function setUp()
	{
		$this->e = new EntityValue_getset_Entity;
	}

	public function testUnexists()
	{
		$this->setExpectedException('Nette\MemberAccessException', 'Cannot write to an undeclared property EntityValue_getset_Entity::$unexists.');
		$this->e->unexists = 3;
	}

	public function testNoMetaMethod()
	{
		$this->e->method = 3;
		$this->assertSame(3, $this->e->method);
		$this->e->method = 'aa';
		$this->assertSame('aa', $this->e->method);
	}

	public function testReadOnly()
	{
		$this->setExpectedException('Nette\MemberAccessException', 'Cannot write to a read-only property EntityValue_getset_Entity::$readOnly.');
		$this->e->readOnly = 'xyz';
	}
}

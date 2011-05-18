<?php

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers _EntityValue::__get
 * @see EntityValue_getter_Test
 */
class EntityValue_get_Test extends TestCase
{
	private $e;

	protected function setUp()
	{
		$this->e = new EntityValue_getset_Entity;
	}

	public function testUnexists()
	{
		$this->setExpectedException('Nette\MemberAccessException', 'Cannot read an undeclared property EntityValue_getset_Entity::$unexists.');
		$this->e->unexists;
	}

	public function testNoMetaMethod()
	{
		$this->assertSame(NULL, $this->e->method);
		$this->e->method = 3;
		$this->assertSame(3, $this->e->method);
	}

}

<?php

require_once __DIR__ . '/../../../../boot.php';

/**
 * @covers _EntityValue::getValue
 * @see EntityValue_getter_Test
 */
class EntityValue_getValue_Test extends TestCase
{
	private $e;

	protected function setUp()
	{
		$this->e = new EntityValue_getset_Entity;
	}

	public function testUnexists()
	{
		$this->setExpectedException('MemberAccessException', 'Cannot read an undeclared property EntityValue_getset_Entity::$unexists.');
		$this->e->gv('unexists');
	}

	public function test()
	{
		$this->e->string = 'xyz';
		$this->assertSame('xyz', $this->e->gv('string'));
	}

}

<?php

require_once __DIR__ . '/../../../../boot.php';

/**
 * @covers Entity::__toString
 */
class Entity_toString_Test extends TestCase
{
	private $r;

	protected function setUp()
	{
		$m = new Model;
		$this->r = $m->testentity;
	}

	public function testPersisted()
	{
		$e = $this->r->getById(1);
		$this->assertSame('1', $e->__toString());
		$this->assertSame('1', (string) $e);
	}

	public function testUnpersisted()
	{
		$e = new TestEntity;
		$this->assertSame('', $e->__toString());
		$this->assertSame('', (string) $e);
	}

	public function testBadId()
	{
		$e = <<<EOT
O:10:"TestEntity":1:{s:20:"\x00_EntityValue\x00values";a:1:{s:2:"id";s:3:"xyz";}}
EOT;
		$e = unserialize($e);
		$e->___event($e, 'create', $this->r);
		$this->assertSame('', $e->__toString());
	}

}

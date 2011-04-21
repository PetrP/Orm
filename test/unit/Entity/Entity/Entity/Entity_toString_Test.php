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
		$e = new TestEntity;
		$e->___event($e, 'load', $this->r, array('id' => 'xyz'));
		$this->assertSame('', $e->__toString());
	}

}

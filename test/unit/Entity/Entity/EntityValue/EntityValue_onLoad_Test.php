<?php

require_once __DIR__ . '/../../../../boot.php';

/**
 * @covers _EntityValue::onLoad
 */
class EntityValue_onLoad_Test extends TestCase
{
	private $r;

	protected function setUp()
	{
		$m = new Model;
		$this->r = $m->TestEntity;
	}

	public function test()
	{
		$e = $this->r->getById(1);
		$this->assertSame(array(), $this->readAttribute($e, 'valid'));
		$this->assertInternalType('array', $this->readAttribute($e, 'values'));
		$this->assertSame(false, $e->isChanged());
		$this->assertInternalType('array', $this->readAttribute($e, 'rules'));
	}

	public function test2()
	{
		$e = new TestEntity;
		$e->___event($e, 'load', $this->r, array('xxx' => 'yyy'));
		$this->assertSame(array('xxx' => 'yyy'), $this->readAttribute($e, 'values'));
	}

}

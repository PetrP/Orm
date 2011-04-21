<?php

require_once __DIR__ . '/../../../../boot.php';

/**
 * @covers _EntityGeneratingRepository::onLoad
 */
class EntityGeneratingRepository_onLoad_Test extends TestCase
{
	private $r;

	protected function setUp()
	{
		$m = new Model;
		$this->r = $m->testentity;
	}

	public function test()
	{
		$e = new TestEntity;
		$this->assertSame(NULL, $e->getGeneratingRepository(false));
		$e->___event($e, 'load', $this->r, array());
		$this->assertSame($this->r, $e->getGeneratingRepository(false));
	}

}

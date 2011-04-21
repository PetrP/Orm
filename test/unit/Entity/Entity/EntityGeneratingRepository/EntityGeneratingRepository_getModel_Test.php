<?php

require_once __DIR__ . '/../../../../boot.php';

/**
 * @covers _EntityGeneratingRepository::getModel
 */
class EntityGeneratingRepository_getModel_Test extends TestCase
{
	private $m;
	private $r;

	protected function setUp()
	{
		$this->m = new Model;
		$this->r = $this->m->testentity;
	}

	public function testNotNeed()
	{
		$e = new TestEntity;
		$this->assertSame(NULL, $e->getModel(false));
		$e = $this->r->getById(1);
		$this->assertSame($this->m, $e->getModel(false));
	}

	public function testNeed1()
	{
		$e = $this->r->getById(1);
		$this->assertSame($this->m, $e->getModel(true));
		$this->assertSame($this->m, $e->getModel());
	}

	public function testNeed2()
	{
		$this->markTestSkipped();
		$e = new TestEntity;
		$this->setExpectedException('InvalidStateException'); // bug di
		$e->getModel(true);
	}

}

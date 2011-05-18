<?php

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers _EntityGeneratingRepository::getModel
 */
class EntityGeneratingRepository_getModel_Test extends TestCase
{
	private $m;
	private $r;

	protected function setUp()
	{
		$this->m = new RepositoryContainer;
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
		$e = new TestEntity;
		$this->setExpectedException('InvalidStateException', 'TestEntity is not attached to repository.');
		$e->getModel();
	}

	public function testNeedBc()
	{
		$e = new TestEntity;
		$this->assertSame($this->m, $e->getModel(NULL));
	}

}

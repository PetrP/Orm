<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\AttachableEntityFragment::getGeneratingRepository
 */
class AttachableEntityFragment_getGeneratingRepository_Test extends TestCase
{
	private $r;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->r = $m->testentity;
	}

	public function testNotNeed()
	{
		$e = new TestEntity;
		$this->assertSame(NULL, $e->getGeneratingRepository(false));
		$e = $this->r->getById(1);
		$this->assertSame($this->r, $e->getGeneratingRepository(false));
	}

	public function testNeed1()
	{
		$e = $this->r->getById(1);
		$this->assertSame($this->r, $e->getGeneratingRepository(true));
		$this->assertSame($this->r, $e->getGeneratingRepository());
	}

	public function testNeed2()
	{
		$e = new TestEntity;
		$this->setExpectedException('Nette\InvalidStateException', 'TestEntity is not attached to repository.');
		$e->getGeneratingRepository(true);
	}

}

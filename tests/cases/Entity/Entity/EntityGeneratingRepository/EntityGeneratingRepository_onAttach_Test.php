<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\_EntityGeneratingRepository::onAttach
 */
class EntityGeneratingRepository_onAttach_Test extends TestCase
{
	private $r;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->r = $m->testentity;
	}

	public function testNew()
	{
		$e = new TestEntity;
		$this->assertSame(NULL, $e->getGeneratingRepository(false));
		$this->r->attach($e);
		$this->assertSame($this->r, $e->getGeneratingRepository(false));
	}

	public function testAlreadyAttached()
	{
		$e = $this->r->getById(1);
		$this->assertSame($this->r, $e->getGeneratingRepository());
		$this->r->attach($e);
		$this->assertSame($this->r, $e->getGeneratingRepository());
	}

}

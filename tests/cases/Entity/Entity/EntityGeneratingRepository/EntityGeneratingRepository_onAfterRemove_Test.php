<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\_EntityGeneratingRepository::onAfterRemove
 */
class EntityGeneratingRepository_onAfterRemove_Test extends TestCase
{
	private $r;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->r = $m->testentity;
	}

	public function test()
	{
		$e = $this->r->getById(1);
		$this->assertSame($this->r, $e->getGeneratingRepository(false));
		$this->r->remove($e);
		$this->assertSame(NULL, $e->getGeneratingRepository(false));
	}

}

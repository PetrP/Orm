<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\AttachableEntityFragment::onAfterRemove
 */
class AttachableEntityFragment_onAfterRemove_Test extends TestCase
{
	private $r;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->r = $m->testentityrepository;
	}

	public function test()
	{
		$e = $this->r->getById(1);
		$this->assertSame($this->r, $e->getRepository(false));
		$this->r->remove($e);
		$this->assertSame(NULL, $e->getRepository(false));
	}

}

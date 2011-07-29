<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\AttachableEntityFragment::onLoad
 */
class AttachableEntityFragment_onLoad_Test extends TestCase
{
	private $r;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->r = $m->testentityrepository;
	}

	public function test()
	{
		$e = new TestEntity;
		$this->assertSame(NULL, $e->getRepository(false));
		$e->___event($e, 'load', $this->r, array('id' => 1));
		$this->assertSame($this->r, $e->getRepository(false));
	}

}

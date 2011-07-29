<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\AttachableEntityFragment::onAttach
 */
class AttachableEntityFragment_onPersist_Test extends TestCase
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
		$this->r->persist($e);
		$this->assertSame($this->r, $e->getRepository(false));
	}

}

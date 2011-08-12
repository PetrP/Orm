<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\Repository::lazyLoad
 */
class Repository_lazyLoad_Test extends TestCase
{
	private $r;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->r = $m->tests;
	}

	public function test()
	{
		$this->assertSame(array(), $this->r->lazyLoad(new TestEntity, 'xyz'));
	}

}

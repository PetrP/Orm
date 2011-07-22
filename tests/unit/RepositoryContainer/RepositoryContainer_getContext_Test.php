<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\RepositoryContainer::getContext
 */
class RepositoryContainer_getContext_Test extends TestCase
{
	private $m;

	protected function setUp()
	{
		$this->m = new RepositoryContainer;
	}

	public function test()
	{
		$this->assertInstanceOf('Orm\IServiceContainer', $this->m->getContext());
	}

	public function testSame()
	{
		$this->assertSame($this->m->getContext(), $this->m->getContext());
	}

}

<?php

use Orm\RepositoryContainer;
use Orm\ServiceContainer;

/**
 * @covers Orm\RepositoryContainer::setContext
 */
class RepositoryContainer_setContext_Test extends TestCase
{
	private $m;

	protected function setUp()
	{
		$this->m = new RepositoryContainer;
	}

	public function test()
	{
		$c = new ServiceContainer;
		$this->m->setContext($c);
		$this->assertSame($c, $this->m->getContext());
	}

	public function testReturns()
	{
		$this->assertSame($this->m, $this->m->setContext(new ServiceContainer));
	}
}

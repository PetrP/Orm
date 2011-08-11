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

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RepositoryContainer', 'setContext');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

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

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RepositoryContainer', 'getContext');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

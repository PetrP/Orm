<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\RepositoryContainer::setContext
 */
class RepositoryContainer_setContext_Test extends TestCase
{

	public function test()
	{
		$m = new RepositoryContainer;
		$this->setExpectedException('Orm\DeprecatedException', 'Orm\RepositoryContainer::setContext() is deprecated; use Orm\RepositoryContainer::__construct() instead.');
		$m->setContext();
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\RepositoryContainer', 'setContext');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

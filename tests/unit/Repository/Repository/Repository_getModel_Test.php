<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\Repository::getModel
 * @covers Orm\Repository::__construct
 */
class Repository_getModel_Test extends TestCase
{

	public function test()
	{
		$m = new RepositoryContainer;
		$r = new TestsRepository($m);
		$this->assertSame($m, $r->getModel());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Repository', 'getModel');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

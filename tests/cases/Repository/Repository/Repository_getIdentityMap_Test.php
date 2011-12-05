<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\Repository::getIdentityMap
 * @covers Orm\Repository::__construct
 */
class Repository_getIdentityMap_Test extends TestCase
{

	public function test()
	{
		$m = new RepositoryContainer;
		$r = new TestsRepository($m);
		$this->assertInstanceOf('Orm\IdentityMap', $r->getIdentityMap());
		$this->assertSame($r->getIdentityMap(), $r->getIdentityMap());
		$this->assertAttributeSame($r, 'repository', $r->getIdentityMap());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Repository', 'getIdentityMap');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

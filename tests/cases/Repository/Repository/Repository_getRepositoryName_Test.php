<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\Repository::getRepositoryName
 */
class Repository_getRepositoryName_Test extends TestCase
{

	public function test()
	{
		$r = new TestsRepository(new RepositoryContainer);
		$this->setExpectedException('Orm\DeprecatedException', 'Orm\Repository::getRepositoryName() is deprecated; use get_class($repository) instead');
		$r->getRepositoryName();
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Repository', 'getRepositoryName');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

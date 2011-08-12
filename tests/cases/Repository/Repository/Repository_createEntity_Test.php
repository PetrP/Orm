<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\Repository::createEntity
 */
class Repository_createEntity_Test extends TestCase
{

	public function test()
	{
		$r = new TestsRepository(new RepositoryContainer);
		$this->setExpectedException('Orm\DeprecatedException', 'Orm\Repository::createEntity() is deprecated; use Orm\Repository::hydrateEntity() instead');
		$r->createEntity(array());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Repository', 'createEntity');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

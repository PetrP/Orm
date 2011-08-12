<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\Repository::isEntity
 */
class Repository_isEntity_Test extends TestCase
{

	public function test()
	{
		$r = new TestsRepository(new RepositoryContainer);
		$this->setExpectedException('Orm\DeprecatedException', 'Orm\Repository::isEntity() is deprecated; use Orm\Repository::isAttachableEntity() instead');
		$r->isEntity(new TestEntity);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Repository', 'isEntity');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

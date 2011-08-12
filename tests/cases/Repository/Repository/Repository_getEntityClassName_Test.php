<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\Repository::getEntityClassName
 */
class Repository_getEntityClassName_Test extends TestCase
{
	private $r;

	protected function setUp()
	{
		$this->r = new Repository_getEntityClassNamesRepository(new RepositoryContainer);
	}

	public function testByProperty()
	{
		$this->r->entityClassName = 'Haha';
		$this->assertSame('Haha', $this->r->getEntityClassName());
		$this->assertSame('Haha', $this->r->getEntityClassName(array()));
	}

	public function testDefault()
	{
		$this->r->entityClassName = NULL;
		$this->assertSame('repository_getentityclassname', $this->r->getEntityClassName());
		$this->assertSame('repository_getentityclassname', $this->r->getEntityClassName(array()));
	}

	public function testNamespace()
	{
		if (PHP_VERSION_ID < 50300)
		{
			$this->markTestIncomplete('php 5.2 (namespace)');
		}
		$c = 'Repository_createMapper\Repository_createMapperRepository'; // aby nebyl parse error v php52
		$r = new $c(new RepositoryContainer);
		$this->assertSame('repository_createmapper\repository_createmapper', $r->getEntityClassName());
		$this->assertSame('repository_createmapper\repository_createmapper', $r->getEntityClassName(array()));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Repository', 'getEntityClassName');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

<?php

use Orm\RepositoryContainer;
use Orm\IdentityMap;
use Orm\PerformanceHelper;

/**
 * @covers Orm\IdentityMap::__construct
 */
class IdentityMap_construct_Test extends TestCase
{
	public function test()
	{
		$m = new RepositoryContainer;
		$im = new IdentityMap($m->tests);

		$this->assertAttributeSame($m->tests, 'repository', $im);
		$this->assertAttributeSame($m->tests->events, 'events', $im);
		$this->assertAttributeSame(NULL, 'performanceHelper', $im);
		$this->assertAttributeSame(array('testentity' => true), 'allowedEntities', $im);
	}

	public function testWithPerformenceHelper()
	{
		$m = new RepositoryContainer;
		$ph = new PerformanceHelper($m->tests, new ArrayObject);
		$im = new IdentityMap($m->tests, $ph);

		$this->assertAttributeSame($m->tests, 'repository', $im);
		$this->assertAttributeSame($m->tests->events, 'events', $im);
		$this->assertAttributeSame($ph, 'performanceHelper', $im);
		$this->assertAttributeSame(array('testentity' => true), 'allowedEntities', $im);
	}

	public function testUnexistsEntity()
	{
		$r = new Repository_getEntityClassNamesRepository(new RepositoryContainer, 'TestEntity');
		$r->entityClassName = 'FooBar';

		$this->setExpectedException('Orm\InvalidEntityException', 'Repository_getEntityClassNamesRepository: entity \'FooBar\' does not exists; see property Orm\Repository::$entityClassName or method Orm\IRepository::getEntityClassName()');
		$im = new IdentityMap($r);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\IdentityMap', '__construct');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

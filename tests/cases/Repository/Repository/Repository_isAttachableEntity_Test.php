<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\Repository::isAttachableEntity
 * @covers Orm\IdentityMap::check
 * @covers Orm\IdentityMap::checkEntityClassName
 * @covers Orm\IdentityMap::checkEntityRepository
 */
class Repository_isAttachableEntity_Test extends TestCase
{
	private $r;

	private function r($entityName)
	{
		$this->r = new Repository_getEntityClassNamesRepository(new RepositoryContainer, $entityName);
	}

	public function test()
	{
		$this->r('TestEntity1');
		$this->assertFalse($this->r->isAttachableEntity(new TestEntity));
	}

	public function test2()
	{
		$this->r('TestEntity');
		$this->assertTrue($this->r->isAttachableEntity(new TestEntity));
	}

	public function testException()
	{
		$this->r('TestEntity1');
		$this->setExpectedException('Orm\InvalidEntityException', "Repository_getEntityClassNamesRepository can't work with entity 'TestEntity', only with 'TestEntity1'");
		$this->r->persist(new TestEntity);
	}

	public function testException2()
	{
		$this->r = new Repository_isAttachableEntity_Repository(new RepositoryContainer);
		$this->setExpectedException('Orm\InvalidEntityException', "Repository_isAttachableEntity_Repository can't work with entity 'TestEntity', only with 'TestEntity1', 'TestEntity2' or 'TestEntity3");
		$this->r->persist(new TestEntity);
	}

	public function testRightEntityFromAnotherRepository()
	{
		$r1 = new TestsRepository(new RepositoryContainer);
		$r2 = new TestsRepository(new RepositoryContainer);
		$this->assertFalse($r1->isAttachableEntity($r2->getById(1)));
	}

	public function testRightEntityFromAnotherRepositoryException()
	{
		$r1 = new TestsRepository(new RepositoryContainer);
		$r2 = new TestsRepository(new RepositoryContainer);
		$this->setExpectedException('Orm\InvalidEntityException', "TestEntity#1 is attached to another repository.");
		$r1->persist($r2->getById(1));
	}

	public function testNotExistsEntity()
	{
		$this->setExpectedException('Orm\InvalidEntityException', "Repository_getEntityClassNamesRepository: entity 'Haha' does not exists; see property Orm\\Repository::\$entityClassName or method Orm\\IRepository::getEntityClassName()");
		$this->r('Haha');
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Repository', 'isAttachableEntity');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

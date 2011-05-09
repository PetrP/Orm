<?php

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers Repository::isEntity
 * @covers Repository::checkEntity
 */
class Repository_isEntity_Test extends TestCase
{
	private $r;

	protected function setUp()
	{
		$this->r = new Repository_getEntityClassNamesRepository(new Model);
	}

	public function test()
	{
		$this->r->entityClassName = 'Haha';
		$this->assertFalse($this->r->isEntity(new TestEntity));
	}

	public function test2()
	{
		$this->r->entityClassName = 'TestEntity';
		$this->assertTrue($this->r->isEntity(new TestEntity));
	}

	public function testException()
	{
		$this->r->entityClassName = 'Haha';
		$this->setExpectedException('UnexpectedValueException', "Repository_getEntityClassNamesRepository can't work with entity 'TestEntity', only with 'Haha'");
		$this->r->persist(new TestEntity);
	}

	public function testException2()
	{
		$this->r = new Repository_isEntity_Repository(new Model);
		$this->setExpectedException('UnexpectedValueException', "Repository_isEntity_Repository can't work with entity 'TestEntity', only with 'TestEntity1', 'TestEntity2' or 'TestEntity3");
		$this->r->persist(new TestEntity);
	}

	public function testRightEntityFromAnotherRepository()
	{
		$r1 = new TestsRepository(new Model);
		$r2 = new TestsRepository(new Model);
		$this->assertFalse($r1->isEntity($r2->getById(1)));
	}

	public function testRightEntityFromAnotherRepositoryException()
	{
		$r1 = new TestsRepository(new Model);
		$r2 = new TestsRepository(new Model);
		$this->setExpectedException('UnexpectedValueException', "TestEntity#1 is attached to another repository.");
		$r1->persist($r2->getById(1));
	}
}

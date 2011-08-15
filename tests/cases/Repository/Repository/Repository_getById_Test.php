<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\Repository::getById
 */
class Repository_getById_Test extends TestCase
{
	private $r;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->r = $m->tests;
	}

	public function testNull()
	{
		$this->assertNull($this->r->getById(NULL));
	}

	public function testUnknown()
	{
		$this->assertNull($this->r->getById('blabla'));
		$this->assertNull($this->r->getById('blabla'));
	}

	public function testNotScalar()
	{
		$this->setExpectedException('Orm\InvalidArgumentException', "TestsRepository::getById() \$id must be scalar; 'array' given.");
		$this->assertNull($this->r->getById(array()));
	}

	public function testOk()
	{
		$e = $this->r->getById(1);
		$this->assertInstanceOf('TestEntity', $e);
	}

	public function testIdentityMap()
	{
		$e = $this->r->getById(1);
		$ee = $this->r->getById(1);
		$this->assertSame($ee, $e);
	}

	public function testIEntity()
	{
		$e = $this->r->getById(1);
		$this->assertSame($e, $this->r->getById($e));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Repository', 'getById');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

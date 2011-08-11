<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\ArrayMapper::getById
 */
class ArrayMapper_getById_Test extends TestCase
{
	private $m;
	protected function setUp()
	{
		$this->m = new TestsMapper(new TestsRepository(new RepositoryContainer));
	}

	public function testReturnExists()
	{
		$this->assertInstanceOf('TestEntity', $this->m->getById(1));
	}

	public function testReturnUnExists()
	{
		$this->assertSame(NULL, $this->m->getById(666));
	}

	public function testEmpty()
	{
		$this->assertSame(NULL, $this->m->getById(''));
		$this->assertSame(NULL, $this->m->getById(NULL));
		$this->assertSame(NULL, $this->m->getById(false));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ArrayMapper', 'getById');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

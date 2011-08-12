<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\ArrayMapper::createManyToManyMapper
 */
class ArrayMapper_createManyToManyMapper_Test extends TestCase
{
	private $m;
	private $r;
	protected function setUp()
	{
		$this->r = new TestsRepository(new RepositoryContainer);
		$this->m = new TestsMapper($this->r);
	}

	public function testReturn()
	{
		$this->assertInstanceOf('Orm\ArrayManyToManyMapper', $this->m->createManyToManyMapper('f', $this->r, 's'));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ArrayMapper', 'createManyToManyMapper');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

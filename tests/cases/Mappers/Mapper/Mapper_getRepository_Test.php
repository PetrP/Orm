<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\Mapper::getRepository
 * @covers Orm\Mapper::__construct
 */
class Mapper_getRepository_Test extends TestCase
{

	public function test()
	{
		$r = new TestsRepository(new RepositoryContainer);
		$m = new TestsMapper($r);
		$this->assertSame($r, $m->getRepository());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Mapper', 'getRepository');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

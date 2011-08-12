<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\Mapper::getModel
 */
class Mapper_getModel_Test extends TestCase
{

	public function test()
	{
		$model = new RepositoryContainer;
		$m = new TestsMapper(new TestsRepository($model));
		$this->assertSame($model, $m->getModel());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Mapper', 'getModel');
		$this->assertTrue($r->isPublic(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

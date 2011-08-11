<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\Mapper::createConventional
 */
class Mapper_createConventional_Test extends TestCase
{
	public function test()
	{
		$m = new Mapper_createConventional_Mapper(new TestsRepository(new RepositoryContainer));
		$this->assertInstanceOf('Orm\NoConventional', $m->__createConventional());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Mapper', 'createConventional');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

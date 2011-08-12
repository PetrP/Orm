<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\DibiMapper::createConventional
 */
class DibiMapper_createConventional_Test extends TestCase
{
	public function test()
	{
		$m = new DibiMapper_createConventional_DibiMapper(new TestsRepository(new RepositoryContainer));
		$this->assertInstanceOf('Orm\SqlConventional', $m->__createConventional());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DibiMapper', 'createConventional');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

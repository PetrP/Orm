<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\DibiMapper::createCollectionClass
 */
class DibiMapper_createCollectionClass_Test extends TestCase
{
	public function test()
	{
		$m = new DibiMapper_createCollectionClass_DibiMapper(new TestsRepository(new RepositoryContainer));
		$this->assertSame('Orm\DibiCollection', $m->__createCollectionClass());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\DibiMapper', 'createCollectionClass');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\ArrayMapper::createCollectionClass
 */
class ArrayMapper_createCollectionClass_Test extends TestCase
{
	public function test()
	{
		$m = new ArrayMapper_createCollectionClass_ArrayMapper(new TestsRepository(new RepositoryContainer));
		$this->assertSame('Orm\ArrayCollection', $m->__createCollectionClass());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ArrayMapper', 'createCollectionClass');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

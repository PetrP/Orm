<?php

use Orm\PerformanceHelper;
use Orm\RepositoryContainer;

/**
 * @covers Orm\PerformanceHelper::getCache
 */
class PerformanceHelper_getCache_Test extends TestCase
{

	public function test()
	{
		$ph = new PerformanceHelper_Base_PerformanceHelper(new TestsRepository(new RepositoryContainer), new ArrayObject);
		$this->setExpectedException('Orm\DeprecatedException', 'Orm\PerformanceHelper::getCache() is deprecated; use constructor injection instead');
		$ph->__getCache();
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\PerformanceHelper', 'getCache');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertTrue($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

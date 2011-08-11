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
		$ph = new PerformanceHelper_Base_PerformanceHelper(new TestsRepository(new RepositoryContainer));
		$this->assertInstanceOf('Nette\Caching\Cache', $ph->__getCache());
		$this->assertSame('Orm\PerformanceHelper', $ph->__getCache()->getNamespace());
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\PerformanceHelper', 'getCache');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

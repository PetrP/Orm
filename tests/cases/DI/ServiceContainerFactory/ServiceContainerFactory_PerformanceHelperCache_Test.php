<?php

use Orm\ServiceContainerFactory;

/**
 * @covers Orm\ServiceContainerFactory::__construct
 * @covers Orm\ServiceContainerFactory::getPerformanceHelperCacheFactory
 */
class ServiceContainerFactory_PerformanceHelperCache_Test extends TestCase
{

	public function test()
	{
		$f = new ServiceContainerFactory;
		$c = $f->getContainer();
		$cache = $c->getService('performanceHelperCache');
		$this->assertInstanceOf('ArrayAccess', $cache);
		$this->assertInstanceOf('Nette\Caching\Cache', $cache);
		$this->assertSame('Orm\PerformanceHelper', $cache->getNamespace());
	}

	public function testNoEnvironment()
	{
		$f = new ServiceContainerFactory_PerformanceHelperCache_ServiceContainerFactory;
		$f->phc = false;
		$f->__construct();
		$c = $f->getContainer();
		$this->assertFalse($c->hasService('performanceHelperCache'));
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\ServiceContainerFactory', 'getPerformanceHelperCacheFactory');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}
}

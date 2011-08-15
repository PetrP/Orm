<?php

use Orm\ServiceContainerFactory;

/**
 * @covers Orm\ServiceContainerFactory::__construct
 * @covers Orm\ServiceContainerFactory::createPerformanceHelperCache
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

}

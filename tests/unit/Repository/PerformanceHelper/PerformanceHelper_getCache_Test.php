<?php

use Orm\PerformanceHelper;
use Orm\RepositoryContainer;

require_once dirname(__FILE__) . '/../../../boot.php';

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

}

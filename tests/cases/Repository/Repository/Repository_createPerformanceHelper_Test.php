<?php

use Orm\RepositoryContainer;

/**
 * @covers Orm\Repository::createPerformanceHelper
 * @covers Orm\Repository::__construct
 */
class Repository_createPerformanceHelper_Test extends TestCase
{
	private $oldCb;

	private function t($cache)
	{
		$m = new RepositoryContainer;
		$c = $m->getContext()->removeService('performanceHelperCache');
		if ($cache) $c->addService('performanceHelperCache', $cache);
		return new Repository_createPerformanceHelper_Repository($m);
	}

	protected function setUp()
	{
		$this->oldCb = Orm\PerformanceHelper::$keyCallback;
		Orm\PerformanceHelper::$keyCallback = function () {return 'foo';};
	}

	protected function tearDown()
	{
		Orm\PerformanceHelper::$keyCallback = $this->oldCb;
		Repository_createPerformanceHelper_Repository::$ph = NULL;
	}

	public function testOk()
	{
		$r = $this->t(new ArrayObject);
		$this->assertAttributeInstanceOf('Orm\PerformanceHelper', 'performanceHelper', $this->readAttribute($r, 'identityMap'));
	}

	public function testNoCache()
	{
		$r = $this->t(NULL);
		$this->assertAttributeSame(NULL, 'performanceHelper', $this->readAttribute($r, 'identityMap'));
	}

	public function testNoCallback()
	{
		Orm\PerformanceHelper::$keyCallback = NULL;
		$r = $this->t(new ArrayObject);
		$this->assertAttributeSame(NULL, 'performanceHelper', $this->readAttribute($r, 'identityMap'));
	}

	public function testBadCache()
	{
		$this->setExpectedException('Orm\ServiceNotInstanceOfException', "Service 'performanceHelperCache' is not instance of 'ArrayAccess'.");
		$this->t((object) array());
	}

	public function testBadCacheNoCallback()
	{
		Orm\PerformanceHelper::$keyCallback = NULL;
		$r = $this->t((object) array());
		$this->assertAttributeSame(NULL, 'performanceHelper', $this->readAttribute($r, 'identityMap'));
	}

	public function testBadReturn1()
	{
		Repository_createPerformanceHelper_Repository::$ph = new ArrayObject;
		$this->setExpectedException('Orm\BadReturnException', "Repository_createPerformanceHelper_Repository::createPerformanceHelper() must return Orm\\PerformanceHelper or NULL, 'ArrayObject' given");
		$this->t(new ArrayObject);
	}

	public function testBadReturn2()
	{
		Repository_createPerformanceHelper_Repository::$ph = 'foo';
		$this->setExpectedException('Orm\BadReturnException', "Repository_createPerformanceHelper_Repository::createPerformanceHelper() must return Orm\\PerformanceHelper or NULL, 'string' given");
		$this->t(new ArrayObject);
	}

	public function testReflection()
	{
		$r = new ReflectionMethod('Orm\Repository', 'createPerformanceHelper');
		$this->assertTrue($r->isProtected(), 'visibility');
		$this->assertFalse($r->isFinal(), 'final');
		$this->assertFalse($r->isStatic(), 'static');
		$this->assertFalse($r->isAbstract(), 'abstract');
	}

}

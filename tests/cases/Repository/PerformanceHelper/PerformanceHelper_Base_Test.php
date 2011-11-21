<?php

use Orm\PerformanceHelper;
use Orm\RepositoryContainer;

/**
 * @covers Orm\PerformanceHelper::__construct
 * @covers Orm\PerformanceHelper::access
 * @covers Orm\PerformanceHelper::get
 */
class PerformanceHelper_Base_Test extends TestCase
{
	private $r;
	private $originCb;
	private $cache;
	private $cb = __CLASS__;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->r = $m->tests;
		$this->wipe();
		$this->originCb = PerformanceHelper::$keyCallback;
		$this->cache = new PerformanceHelper_ArrayObject;
		PerformanceHelper::$keyCallback = array($this , 'cb');
	}

	private function wipe()
	{
		$r = new ReflectionProperty('Orm\PerformanceHelper', 'toLoad');
		setAccessible($r);
		$r->setValue(NULL);
		PerformanceHelper::$toSave = NULL;
	}

	protected function tearDown()
	{
		PerformanceHelper::$keyCallback = $this->originCb;
		$this->wipe();
	}

	public function cb()
	{
		return $this->cb;
	}

	public function test()
	{
		$h = new PerformanceHelper($this->r, $this->cache);
		$this->assertSame('PerformanceHelper_Base_Test', $this->cache->lastIndex);
		$this->assertAttributeSame('TestsRepository', 'repositoryClass', $h);
		$this->assertAttributeSame(array(), 'access', $h);
		$this->assertAttributeSame(array('TestsRepository' => array()), 'toSave', 'Orm\PerformanceHelper');
		$this->assertAttributeSame(array(), 'toLoad', 'Orm\PerformanceHelper');
		$this->assertSame(NULL, $h->get());

		$h->access(1);
		$h->access(2);

		$this->assertAttributeSame(array(1 => 1, 2 => 2), 'access', $h);
		$this->assertAttributeSame(array('TestsRepository' => array(1 => 1, 2 => 2)), 'toSave', 'Orm\PerformanceHelper');
		$this->assertSame(NULL, $h->get());
	}

	public function testCache()
	{
		$this->cache[__CLASS__]['TestsRepository'] = array(1 => 1, 2 => 2);
		$this->cache['*']['TestsRepository'] = array(3 => 3);
		$h = new PerformanceHelper($this->r, $this->cache);
		$this->assertSame('PerformanceHelper_Base_Test', $this->cache->lastIndex);
		$this->assertAttributeSame('TestsRepository', 'repositoryClass', $h);
		$this->assertAttributeSame(array(), 'access', $h);
		$this->assertAttributeSame(array('TestsRepository' => array()), 'toSave', 'Orm\PerformanceHelper');
		$this->assertAttributeSame(array('TestsRepository' => array(1 => 1, 2 => 2)), 'toLoad', 'Orm\PerformanceHelper');
		$this->assertSame(array(1 => 1, 2 => 2), $h->get());
		$this->assertAttributeSame(array('TestsRepository' => NULL), 'toLoad', 'Orm\PerformanceHelper');
		$this->assertSame(NULL, $h->get());
	}

	public function testCacheStar()
	{
		$this->cb = NULL;
		$this->cache[__CLASS__]['TestsRepository'] = array(1 => 1, 2 => 2);
		$this->cache['*']['TestsRepository'] = array(3 => 3);
		$h = new PerformanceHelper($this->r, $this->cache);
		$this->assertSame('*', $this->cache->lastIndex);
		$this->assertAttributeSame('TestsRepository', 'repositoryClass', $h);
		$this->assertAttributeSame(array(3 => 3), 'access', $h);
		$this->assertAttributeSame(array('TestsRepository' => array(3 => 3)), 'toSave', 'Orm\PerformanceHelper');
		$this->assertAttributeSame(array('TestsRepository' => array(3 => 3)), 'toLoad', 'Orm\PerformanceHelper');
		$this->assertSame(array(3 => 3), $h->get());
		$this->assertAttributeSame(array('TestsRepository' => NULL), 'toLoad', 'Orm\PerformanceHelper');
		$this->assertSame(NULL, $h->get());

		$h->access(2);
		$this->assertAttributeSame(array('TestsRepository' => array(3 => 3, 2 => 2)), 'toSave', 'Orm\PerformanceHelper');
	}

	/**
	 * @covers Orm\Repository::getById
	 */
	public function testGetById_IdNotPerformedNotExist()
	{
		$this->cache[__CLASS__]['TestsRepository'] = array(1 => 1, 2 => 2, 4 => 4, 999 => 999);
		$h = new PerformanceHelper($this->r, $this->cache);
		$this->assertSame('PerformanceHelper_Base_Test', $this->cache->lastIndex);

		$im = $this->readAttribute($this->r, 'identityMap');
		$im->__construct($this->r, $h);

		$e = $this->r->getById(3);
		$this->assertSame(NULL, $e);

		$entities = $this->readAttribute($im, 'entities');
		$this->assertSame(5, count($entities));
		$this->assertSame(false, $entities[999]);
		$this->assertSame(false, $entities[4]);
		$this->assertSame(false, $entities[3]);
		$this->assertSame($this->r->getById(2), $entities[2]);
		$this->assertSame($this->r->getById(1), $entities[1]);
	}

	/**
	 * @covers Orm\Repository::getById
	 */
	public function testGetById_IdNotPerformedExist()
	{
		$this->cache[__CLASS__]['TestsRepository'] = array(1 => 1, 4 => 4, 999 => 999);
		$h = new PerformanceHelper($this->r, $this->cache);
		$this->assertSame('PerformanceHelper_Base_Test', $this->cache->lastIndex);

		$im = $this->readAttribute($this->r, 'identityMap');
		$im->__construct($this->r, $h);

		$e = $this->r->getById(2);
		$this->assertInstanceOf('TestEntity', $e);
		$this->assertSame(2, $e->id);

		$entities = $this->readAttribute($im, 'entities');
		$this->assertSame(4, count($entities));
		$this->assertSame(false, $entities[999]);
		$this->assertSame(false, $entities[4]);
		$this->assertSame($this->r->getById(2), $entities[2]);
		$this->assertSame($this->r->getById(1), $entities[1]);
	}

	/**
	 * @covers Orm\Repository::getById
	 */
	public function testGetById_IdIsPerformedNotExist()
	{
		$this->cache[__CLASS__]['TestsRepository'] = array(1 => 1, 2 => 2, 4 => 4, 999 => 999);
		$h = new PerformanceHelper($this->r, $this->cache);
		$this->assertSame('PerformanceHelper_Base_Test', $this->cache->lastIndex);

		$im = $this->readAttribute($this->r, 'identityMap');
		$im->__construct($this->r, $h);

		$e = $this->r->getById(4);
		$this->assertSame(NULL, $e);

		$entities = $this->readAttribute($im, 'entities');
		$this->assertSame(4, count($entities));
		$this->assertSame(false, $entities[999]);
		$this->assertSame(false, $entities[4]);
		$this->assertSame($this->r->getById(2), $entities[2]);
		$this->assertSame($this->r->getById(1), $entities[1]);
	}

	/**
	 * @covers Orm\Repository::getById
	 */
	public function testGetById_IdIsPerformedExist()
	{
		$this->cache[__CLASS__]['TestsRepository'] = array(1 => 1, 2 => 2, 4 => 4, 999 => 999);
		$h = new PerformanceHelper($this->r, $this->cache);
		$this->assertSame('PerformanceHelper_Base_Test', $this->cache->lastIndex);

		$im = $this->readAttribute($this->r, 'identityMap');
		$im->__construct($this->r, $h);

		$e = $this->r->getById(1);
		$this->assertInstanceOf('TestEntity', $e);
		$this->assertSame(1, $e->id);

		$entities = $this->readAttribute($im, 'entities');
		$this->assertSame(4, count($entities));
		$this->assertSame(false, $entities[999]);
		$this->assertSame(false, $entities[4]);
		$this->assertSame($this->r->getById(2), $entities[2]);
		$this->assertSame($this->r->getById(1), $entities[1]);
		$this->assertSame($e, $entities[1]);
	}

	public function testKeyLongerThen50()
	{
		PerformanceHelper::$keyCallback = function () { return str_repeat('a', 51); };
		$this->assertSame(51, strlen(callback(PerformanceHelper::$keyCallback)->invoke()));
		$h = new PerformanceHelper($this->r, $this->cache);
		$this->assertSame('aaaaaaaaaaaaaaaaaaaa1bb77918e5695c944be02c16ae29b25e', $this->cache->lastIndex);
	}

}

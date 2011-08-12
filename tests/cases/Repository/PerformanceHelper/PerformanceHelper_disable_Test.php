<?php

use Orm\RepositoryContainer;
use Orm\PerformanceHelper;

require_once dirname(__FILE__) . '/../../../boot.php';

/**
 * @covers Orm\PerformanceHelper::__construct
 */
class PerformanceHelper_disable_Test extends TestCase
{
	private $r;
	private $originCb;
	private $ph;
	private $originToSave;
	private $originToLoad;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->r = $m->tests;
		$this->originCb = PerformanceHelper::$keyCallback;
		$this->originToSave = PerformanceHelper::$toSave;
		$this->originToLoad = $this->readAttribute('Orm\PerformanceHelper', 'toLoad');
		PerformanceHelper::$keyCallback = NULL;
		PerformanceHelper::$toSave = NULL;
		$this->ph = new PerformanceHelper($this->r, new ArrayObject);
	}

	protected function tearDown()
	{
		PerformanceHelper::$keyCallback = $this->originCb;
		PerformanceHelper::$toSave = $this->originToSave;
	}

	public function testConstruct()
	{
		$this->assertAttributeSame(NULL, 'repositoryClass', $this->ph);
		$this->assertAttributeSame(array(), 'access', $this->ph);
		$this->assertSame(NULL, PerformanceHelper::$toSave);
		$this->assertAttributeEmpty('toLoad', 'Orm\PerformanceHelper');
		$this->assertAttributeSame($this->originToLoad, 'toLoad', 'Orm\PerformanceHelper');
	}

	public function testAccess()
	{
		$this->ph->access(123);
		$this->assertAttributeSame(NULL, 'repositoryClass', $this->ph);
		$this->assertAttributeSame(array(123 => 123), 'access', $this->ph);
		$this->assertSame(NULL, PerformanceHelper::$toSave);
		$this->assertAttributeEmpty('toLoad', 'Orm\PerformanceHelper');
		$this->assertAttributeSame($this->originToLoad, 'toLoad', 'Orm\PerformanceHelper');
	}

	public function testGet()
	{
		$this->assertSame(NULL, $this->ph->get());
		$this->assertAttributeSame(NULL, 'repositoryClass', $this->ph);
		$this->assertAttributeSame(array(), 'access', $this->ph);
		$this->assertSame(NULL, PerformanceHelper::$toSave);
		$this->assertAttributeEmpty('toLoad', 'Orm\PerformanceHelper');
		$this->assertAttributeSame($this->originToLoad, 'toLoad', 'Orm\PerformanceHelper');
	}

}

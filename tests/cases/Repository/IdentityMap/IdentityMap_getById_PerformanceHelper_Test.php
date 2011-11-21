<?php

use Orm\RepositoryContainer;
use Orm\IdentityMap;
use Orm\PerformanceHelper;

/**
 * @covers Orm\IdentityMap::getById
 */
class IdentityMap_getById_PerformanceHelper_Test extends TestCase
{
	private $im;
	private $r;
	private $ph;

	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->r = $m->tests;
		$this->ph = new IdentityMap_getById_PerformanceHelper;
		$this->im = $this->readAttribute($this->r, 'identityMap');
		$this->im->__construct($this->r, $this->ph);
	}

	public function testIdNotPerformedNotExist()
	{
		$this->ph->get = array(1 => 1, 2 => 2, 4 => 4, 999 => 999);

		$e = $this->im->getById(3);
		$this->assertSame(NULL, $e);

		$entities = $this->readAttribute($this->im, 'entities');
		$this->assertSame(4, count($entities));
		$this->assertSame(false, $entities[999]);
		$this->assertSame(false, $entities[4]);
		$this->assertSame(false, isset($entities[3]));
		$this->assertSame($this->r->getById(2), $entities[2]);
		$this->assertSame($this->r->getById(1), $entities[1]);
	}

	public function testIdNotPerformedExist()
	{
		$this->ph->get = array(1 => 1, 4 => 4, 999 => 999);

		$e = $this->im->getById(2);
		$this->assertInstanceOf('TestEntity', $e);
		$this->assertSame(2, $e->id);

		$entities = $this->readAttribute($this->im, 'entities');
		$this->assertSame(4, count($entities));
		$this->assertSame(false, $entities[999]);
		$this->assertSame(false, $entities[4]);
		$this->assertSame($this->r->getById(2), $entities[2]);
		$this->assertSame($this->r->getById(1), $entities[1]);
	}

	public function testIdIsPerformedNotExist()
	{
		$this->ph->get = array(1 => 1, 2 => 2, 4 => 4, 999 => 999);

		$e = $this->r->getById(4);
		$this->assertSame(NULL, $e);

		$entities = $this->readAttribute($this->im, 'entities');
		$this->assertSame(4, count($entities));
		$this->assertSame(false, $entities[999]);
		$this->assertSame(false, $entities[4]);
		$this->assertSame($this->r->getById(2), $entities[2]);
		$this->assertSame($this->r->getById(1), $entities[1]);
	}

	public function testIdIsPerformedExist()
	{
		$this->ph->get = array(1 => 1, 2 => 2, 4 => 4, 999 => 999);

		$e = $this->r->getById(1);
		$this->assertInstanceOf('TestEntity', $e);
		$this->assertSame(1, $e->id);

		$entities = $this->readAttribute($this->im, 'entities');
		$this->assertSame(4, count($entities));
		$this->assertSame(false, $entities[999]);
		$this->assertSame(false, $entities[4]);
		$this->assertSame($this->r->getById(2), $entities[2]);
		$this->assertSame($this->r->getById(1), $entities[1]);
		$this->assertSame($e, $entities[1]);
	}


}

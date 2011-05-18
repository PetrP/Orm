<?php

use Orm\RepositoryContainer;

require_once dirname(__FILE__) . '/../../../../boot.php';

/**
 * @covers _EntityValue::isChanged
 */
class EntityValue_isChanged_Test extends TestCase
{
	private $r;
	protected function setUp()
	{
		$m = new RepositoryContainer;
		$this->r = $m->TestEntity;
		$this->e = new TestEntity;
	}

	public function testCreate()
	{
		$e = new TestEntity;
		$this->assertSame(true, $e->isChanged());
	}

	public function testLoad()
	{
		$e = $this->r->getById(1);
		$this->assertSame(false, $e->isChanged());
	}

	public function testSet()
	{
		$e = $this->r->getById(1);
		$e->string = 'xyz';
		$this->assertSame(true, $e->isChanged());
	}

	public function testPersist()
	{
		$e = $this->r->getById(1);
		$e->string = 'xyz';
		$this->r->persist($e);
		$this->assertSame(false, $e->isChanged());
	}

	public function testGet()
	{
		$e = $this->r->getById(1);
		$e->string;
		$this->assertSame(false, $e->isChanged());
	}

	public function testRemove()
	{
		$e = $this->r->getById(1);
		$this->r->remove($e);
		$this->assertSame(true, $e->isChanged());
	}

}

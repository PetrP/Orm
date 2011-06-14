<?php

use Orm\DibiManyToManyMapper;
use Orm\ManyToMany;
use Orm\RepositoryContainer;

require_once __DIR__ . '/../../../boot.php';

/**
 * @covers Orm\DibiManyToManyMapper::attach
 */
class DibiManyToManyMapper_attach_Test extends TestCase
{
	private $mm;
	private $m;

	protected function setUp()
	{
		$c = new DibiConnection(array('lazy' => true));
		$this->mm = new DibiManyToManyMapper($c);
		$this->m = new ManyToMany(new TestEntity, new TestsRepository(new RepositoryContainer), 'foo', 'bar', true);
	}

	public function test()
	{
		$this->mm->parentParam = 'x';
		$this->mm->childParam = 'y';
		$this->mm->table = 't';
		$this->mm->attach($this->m);
		$this->assertTrue(true);
	}

	public function testNoChildParam()
	{
		$this->mm->parentParam = 'y';
		$this->mm->table = 't';
		$this->setExpectedException('Nette\InvalidStateException', 'Orm\DibiManyToManyMapper::$childParam is required');
		$this->mm->attach($this->m);
	}

	public function testNoParentParam()
	{
		$this->mm->childParam = 'x';
		$this->mm->table = 't';
		$this->setExpectedException('Nette\InvalidStateException', 'Orm\DibiManyToManyMapper::$parentParam is required');
		$this->mm->attach($this->m);
	}

	public function testNoTable()
	{
		$this->mm->childParam = 'x';
		$this->mm->parentParam = 'y';
		$this->setExpectedException('Nette\InvalidStateException', 'Orm\DibiManyToManyMapper::$table is required');
		$this->mm->attach($this->m);
	}
}

<?php

use Orm\EntityIterator;
use Orm\RepositoryContainer;
use Nette\Object;

abstract class EntityIterator_Base_Test extends TestCase
{
	protected $d;
	protected $i;
	protected $r;
	protected function setUp()
	{
		$this->d = new EntityIterator_Driver;
		$m = new RepositoryContainer;
		$r = new DibiResult($this->d, array());
		$i = new DibiResultIterator($r);
		$this->r = $m->tests;
		$this->i = new EntityIterator($this->r, $i);
	}
}

class EntityIterator_Driver extends Object implements IDibiResultDriver
{
	private $pos = 0;
	public $count = 3;

	public function getRowCount()
	{
		return $this->count;
	}

	public function seek($row)
	{
		throw new Exception();
	}

	public function fetch($type)
	{
		$this->pos++;
		if ($this->pos > $this->count) return false;
		return array('id' => $this->pos);
	}

	public function free()
	{
		throw new Exception();
	}

	public function getResultColumns()
	{
		throw new Exception();
	}

	public function getResultResource()
	{
		throw new Exception();
	}

	public function unescape($value, $type)
	{
		throw new Exception();
	}

}

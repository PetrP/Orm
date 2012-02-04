<?php

use Orm\HydrateEntityIterator;
use Orm\RepositoryContainer;
use Orm\Object;

class HydrateEntityIterator_Base_IteratorAggregate implements IteratorAggregate
{
	private $array = array();
	public function __construct(array $array)
	{
		$this->array = $array;
	}
	public function getIterator()
	{
		return new ArrayIterator($this->array);
	}
}

abstract class HydrateEntityIterator_Base_Test extends TestCase
{
	protected $d;
	protected $i;
	protected $r;
	protected function setUp()
	{
		$this->d = new HydrateEntityIterator_Driver;
		$m = new RepositoryContainer;
		$r = new DibiResult($this->d, array());
		$i = new DibiResultIterator($r);
		$this->r = $m->tests;
		$this->i = new HydrateEntityIterator($this->r, $i);
	}
}

class HydrateEntityIterator_Driver extends Object implements IDibiResultDriver
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
		throw new DibiNotSupportedException;
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

<?php

use Orm\DibiResultWrapper;
use Orm\Object;
use Orm\RepositoryContainer;

class DibiResultWrapper_Driver extends Object implements IDibiResultDriver
{
	private $pos = 0;
	public $count = 2;
	public $operations = array();

	public function getRowCount()
	{
		return $this->count;
	}

	public function seek($row)
	{
		$this->operations[] = 'seek#' . $row;
		if ($row >= $this->count) return false;
		$this->pos = $row;
		return true;
	}

	public function fetch($type)
	{
		$this->operations[] = 'fetch#' . $this->pos;
		if ($this->pos >= $this->count) return false;
		$this->pos++;
		return array('id' => $this->pos);
	}

	public function free()
	{
		throw new Exception;
	}

	public function getResultColumns()
	{
		throw new DibiNotSupportedException;
	}

	public function getResultResource()
	{
		throw new Exception;
	}

	public function unescape($value, $type)
	{
		throw new Exception;
	}

}

abstract class DibiResultWrapper_Base_Test extends TestCase
{

	protected $d;
	protected $repository;
	protected $dibiResult;
	protected $w;
	protected function setUp()
	{
		$this->d = new DibiResultWrapper_Driver;
		$m = new RepositoryContainer;
		$this->dibiResult = new DibiResult($this->d);
		$this->repository = $m->tests;
		$this->w = new DibiResultWrapper($this->repository, $this->dibiResult);
	}

}

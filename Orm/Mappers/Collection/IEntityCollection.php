<?php

namespace Orm;

use Countable;
use IteratorAggregate;
use Dibi;

require_once dirname(__FILE__) . '/bc.php';

interface IEntityCollection extends Countable, IteratorAggregate, IModelDataSource
{
	public function orderBy($row, $direction = Dibi::ASC);
	public function applyLimit($limit, $offset = NULL);
	public function fetch();
	public function fetchAll();
	public function fetchAssoc($assoc);
	public function fetchPairs($key = NULL, $value = NULL);

	public function toArrayCollection();
	public function toCollection();

	public function findBy(array $where);
	public function getBy(array $where);
}

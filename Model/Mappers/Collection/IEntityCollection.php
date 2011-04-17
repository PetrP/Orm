<?php

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

	// todo
	//abstract protected function findBy(array $where);
	//abstract protected function getBy(array $where);
}

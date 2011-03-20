<?php

require_once dirname(__FILE__) . '/bc.php';

interface IEntityCollection extends IDataSource, IModelDataSource
{
	// todo
	//abstract protected function findBy(array $where);
	//abstract protected function getBy(array $where);
}

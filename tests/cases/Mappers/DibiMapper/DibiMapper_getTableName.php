<?php

use Orm\DibiMapper;
use Orm\Repository;

class DibiMapper_getTableName_DibiMapper extends DibiMapper
{
	public function __getTableName()
	{
		return $this->getTableName();
	}
}

class DibiMapper_getTableName_Repository extends Repository
{
	protected $entityClassName = 'TestEntity';
	public function __setRepositoryName($rn)
	{
		$r = new ReflectionProperty('Orm\Repository', 'repositoryName');
		setAccessible($r);
		$r->setValue($this, $rn);
	}
}

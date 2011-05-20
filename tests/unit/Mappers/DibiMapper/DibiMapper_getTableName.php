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
	public function __setRepositoryName($rn)
	{
		$r = new ReflectionProperty('Orm\Repository', 'repositoryName');
		$r->setAccessible(true);
		$r->setValue($this, $rn);
	}
}

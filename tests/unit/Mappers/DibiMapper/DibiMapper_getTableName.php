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
		if (PHP_VERSION_ID < 50300)
		{
			throw new PHPUnit_Framework_IncompleteTestError('php 5.2 (setAccessible)');
		}
		$r = new ReflectionProperty('Orm\Repository', 'repositoryName');
		$r->setAccessible(true);
		$r->setValue($this, $rn);
	}
}

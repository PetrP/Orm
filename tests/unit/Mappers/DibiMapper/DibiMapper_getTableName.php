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
		$helper = $this->getModel()->getContext()->getService('repositoryHelper', 'Orm\RepositoryHelper');
		$r = new ReflectionProperty('Orm\RepositoryHelper', 'normalizeRepositoryCache');
		setAccessible($r);
		$cache = $r->getValue($helper);
		$cache[get_class($this)] = $rn;
		$r->setValue($helper, $cache);
	}
}

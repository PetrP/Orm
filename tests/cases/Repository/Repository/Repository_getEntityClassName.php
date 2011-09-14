<?php

use Orm\Repository;
use Orm\ArrayMapper;
use Orm\RepositoryHelper;
use Orm\IRepository;

class Repository_getEntityClassNamesRepository extends Repository
{
	public $entityClassName;
}
class Repository_getEntityClassNamesMapper extends ArrayMapper
{
}
class Repository_getEntityClassNames_RepositoryHelper extends RepositoryHelper
{
	public $name;
	public function normalizeRepository(IRepository $repository)
	{
		return $this->name;
	}
}

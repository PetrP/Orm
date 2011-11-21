<?php

use Orm\Repository;
use Orm\ArrayMapper;
use Orm\RepositoryHelper;
use Orm\IRepository;
use Orm\IRepositoryContainer;

class Repository_getEntityClassNamesRepository extends Repository
{
	public $entityClassName;
	public function __construct(IRepositoryContainer $model, $entityClassName)
	{
		$this->entityClassName = $entityClassName;
		parent::__construct($model);
	}
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

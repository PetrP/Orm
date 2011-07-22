<?php

use Orm\Repository;
use Orm\Entity;
use Orm\SqlConventional;
use Orm\RepositoryContainer;

/** @mapper Orm\DibiMapper */
class SqlConventional_TestRepository extends Repository
{
	protected $entityClassName = 'SqlConventional_TestEntity';
}

/**
 * @property SqlConventional_TestEntity $aaa {1:1 SqlConventional_Test}
 * @property SqlConventional_TestEntity|NULL $bBB {m:1 SqlConventional_Test}
 */
class SqlConventional_TestEntity extends Entity
{

}

class MockSqlConventional extends SqlConventional
{

	public function __construct()
	{
		$r = new SqlConventional_TestRepository(new RepositoryContainer);
		parent::__construct($r->mapper);
	}

}

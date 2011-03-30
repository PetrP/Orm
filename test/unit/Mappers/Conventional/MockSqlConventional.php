<?php

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
		$r = new SqlConventional_TestRepository('sqlconventional_test', new Model);
		parent::__construct($r->mapper);
	}

}

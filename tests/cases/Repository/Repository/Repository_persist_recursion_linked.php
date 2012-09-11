<?php

use Orm\Repository;

/**
 * @property Repository_persist_recursion_linked3_Entity $11 {1:1 Repository_persist_recursion_linked3_Repository $11}
 */
class Repository_persist_recursion_linked3_Entity extends TestEntity
{
}

/** @mapper TestsMapper */
class Repository_persist_recursion_linked3_Repository extends Repository
{
	protected $entityClassName = 'Repository_persist_recursion_linked3_Entity';
}

/**
 * @property Repository_persist_recursion_linked5_Entity|NULL $11 {1:1 Repository_persist_recursion_linked5_Repository $11}
 */
class Repository_persist_recursion_linked4_Entity extends TestEntity
{
}

/** @mapper TestsMapper */
class Repository_persist_recursion_linked4_Repository extends Repository
{
	protected $entityClassName = 'Repository_persist_recursion_linked4_Entity';
}

/**
 * @property Repository_persist_recursion_linked4_Entity $11 {1:1 Repository_persist_recursion_linked4_Repository $11}
 */
class Repository_persist_recursion_linked5_Entity extends TestEntity
{
}

/** @mapper TestsMapper */
class Repository_persist_recursion_linked5_Repository extends Repository
{
	protected $entityClassName = 'Repository_persist_recursion_linked5_Entity';
}

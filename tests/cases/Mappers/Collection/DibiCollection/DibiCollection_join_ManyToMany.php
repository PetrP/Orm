<?php

use Orm\Entity;
use Orm\Repository;
use Orm\IRepository;

/**
 * @property string $name
 * @property $joins {m:m DibiCollection_join_ManyToMany2_ joins map}
 * @property $joinsNoParam {m:m DibiCollection_join_ManyToMany2_}
 */
class DibiCollection_join_ManyToMany1_Entity extends Entity
{

}

/**
 * @property string $name
 * @property $joins {m:m DibiCollection_join_ManyToMany1_ joins}
 */
class DibiCollection_join_ManyToMany2_Entity extends Entity
{

}

class DibiCollection_join_ManyToMany1_Repository extends Repository
{
	protected $entityClassName = 'DibiCollection_join_ManyToMany1_Entity';

	protected function createMapper()
	{
		return new DibiCollection_join_ManyToMany_Mapper($this);
	}
}

class DibiCollection_join_ManyToMany2_Repository extends DibiCollection_join_ManyToMany1_Repository
{
	protected $entityClassName = 'DibiCollection_join_ManyToMany2_Entity';
}

class DibiCollection_join_ManyToMany_Mapper extends DibiCollection_join_Mapper
{
	public function createManyToManyMapper($param, IRepository $targetRepository, $targetParam)
	{
		$m =  parent::createManyToManyMapper($param, $targetRepository, $targetParam);
		if ($param === 'joins')
		{
			$m->table = 'mm';
			$m->parentParam = 'parent_id';
			$m->childParam = 'child_id';
		}
		if ($param === 'joinsNoParam')
		{
			$m->table = 'mm2';
			$m->parentParam = 'parent_id';
			$m->childParam = 'child_id';
		}
		return $m;
	}
}

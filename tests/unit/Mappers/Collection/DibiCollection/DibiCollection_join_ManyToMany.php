<?php

/**
 * @property string $name
 * @property $joins {m:m DibiCollection_join_ManyToMany2_ joins map}
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
	public function createManyToManyMapper($firstParam, IRepository $repository, $secondParam)
	{
		$m =  parent::createManyToManyMapper($firstParam, $repository, $secondParam);
		if ($firstParam === 'joins')
		{
			$m->table = 'mm';
			$m->firstParam = 'first_id';
			$m->secondParam = 'second_id';
		}
		return $m;
	}
}

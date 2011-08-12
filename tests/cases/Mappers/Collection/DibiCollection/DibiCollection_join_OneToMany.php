<?php

use Orm\Entity;
use Orm\Repository;

/**
 * @property string $name
 * @property $joins {1:m DibiCollection_join_OneToMany2_ join}
 * @property DibiCollection_join2_Entity $join {m:1 DibiCollection_join_OneToMany2_}
 */
class DibiCollection_join_OneToMany1_Entity extends Entity
{

}

/**
 * @property string $name
 * @property DibiCollection_join1_Entity $join {m:1 DibiCollection_join_OneToMany1_}
 * @property $joins {1:m DibiCollection_join_OneToMany1_ join}
 */
class DibiCollection_join_OneToMany2_Entity extends Entity
{

}

class DibiCollection_join_OneToMany1_Repository extends Repository
{
	protected $entityClassName = 'DibiCollection_join_OneToMany1_Entity';

	protected function createMapper()
	{
		return new DibiCollection_join_OneToMany_Mapper($this);
	}
}

class DibiCollection_join_OneToMany2_Repository extends DibiCollection_join_OneToMany1_Repository
{
	protected $entityClassName = 'DibiCollection_join_OneToMany2_Entity';
}

class DibiCollection_join_OneToMany_Mapper extends DibiCollection_join_Mapper
{
}

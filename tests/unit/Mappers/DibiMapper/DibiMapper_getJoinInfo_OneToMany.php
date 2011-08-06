<?php

use Orm\Entity;
use Orm\Repository;

/**
 * @property string $name
 * @property $joins {1:m DibiMapper_getJoinInfo2_OneToMany_ join}
 * @property DibiMapper_getJoinInfo2_Entity $join {m:1 DibiMapper_getJoinInfo2_OneToMany_}
 */
class DibiMapper_getJoinInfo1_OneToMany_Entity extends Entity
{

}

/**
 * @property string $name
 * @property DibiMapper_getJoinInfo1_Entity $join {m:1 DibiMapper_getJoinInfo1_OneToMany_}
 * @property $joins {1:m DibiMapper_getJoinInfo1_OneToMany_ join}
 */
class DibiMapper_getJoinInfo2_OneToMany_Entity extends Entity
{

}

class DibiMapper_getJoinInfo1_OneToMany_Repository extends Repository
{
	protected $entityClassName = 'DibiMapper_getJoinInfo1_OneToMany_Entity';

	protected function createMapper()
	{
		return new DibiMapper_getJoinInfo_OneToMany_Mapper($this);
	}
}

class DibiMapper_getJoinInfo2_OneToMany_Repository extends DibiMapper_getJoinInfo1_OneToMany_Repository
{
	protected $entityClassName = 'DibiMapper_getJoinInfo2_OneToMany_Entity';
}

class DibiMapper_getJoinInfo_OneToMany_Mapper extends DibiCollection_join_OneToMany_Mapper
{
	public $c;
	protected function createConventional()
	{
		if ($this->c) return $this->c;
		return parent::createConventional();
	}
}

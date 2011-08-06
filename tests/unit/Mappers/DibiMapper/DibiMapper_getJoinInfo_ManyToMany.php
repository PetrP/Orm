<?php

use Orm\Entity;
use Orm\Repository;
use Orm\IRepository;
use Orm\SqlConventional;

/**
 * @property string $name
 * @property $joins {m:m DibiMapper_getJoinInfo2_ManyToMany_ joins map}
 */
class DibiMapper_getJoinInfo1_ManyToMany_Entity extends Entity
{

}

/**
 * @property string $name
 * @property $joins {m:m DibiMapper_getJoinInfo1_ManyToMany_ joins}
 */
class DibiMapper_getJoinInfo2_ManyToMany_Entity extends Entity
{

}

class DibiMapper_getJoinInfo1_ManyToMany_Repository extends Repository
{
	protected $entityClassName = 'DibiMapper_getJoinInfo1_ManyToMany_Entity';

	protected function createMapper()
	{
		return new DibiMapper_getJoinInfo_ManyToMany_Mapper($this);
	}
}

class DibiMapper_getJoinInfo2_ManyToMany_Repository extends DibiMapper_getJoinInfo1_ManyToMany_Repository
{
	protected $entityClassName = 'DibiMapper_getJoinInfo2_ManyToMany_Entity';
}

class DibiMapper_getJoinInfo_ManyToMany_Mapper extends DibiCollection_join_ManyToMany_Mapper
{
	public $c;
	protected function createConventional()
	{
		if ($this->c) return $this->c;
		return new DibiMapper_getJoinInfo_ManyToMany_Conventional($this);
	}
}

class DibiMapper_getJoinInfo_ManyToMany_Conventional extends SqlConventional
{
	public $info = array();

	protected function storageFormat($key)
	{
		$this->info['storageFormat'][] = $key;
		return parent::storageFormat($key);
	}

	protected function entityFormat($key)
	{
		$this->info['entityFormat'][] = $key;
		return parent::entityFormat($key);
	}
}

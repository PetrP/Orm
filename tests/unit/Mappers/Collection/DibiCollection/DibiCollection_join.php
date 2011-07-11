<?php

use Orm\Entity;
use Orm\Repository;
use Orm\DibiMapper;
use Orm\ArrayMapper;

/**
 * @property string $name
 * @property DibiCollection_join2_Entity $join2 {m:1 DibiCollection_join2_}
 * @property DibiCollection_join3_Entity $join3 {m:1 DibiCollection_join3_}
 * @property DibiCollection_join4_Entity $join4 {m:1 DibiCollection_join4_}
 */
class DibiCollection_join1_Entity extends Entity
{

}

/**
 * @property string $name
 * @property DibiCollection_join1_Entity $join1 {m:1 DibiCollection_join1_}
 * @property DibiCollection_join2_Entity $joinBadMapper {m:1 DibiCollection_joinBadMapper_}
 * @property DibiCollection_join2_Entity $joinDifferentConnection {m:1 DibiCollection_joinDifferentConnection_}
 * @property DibiCollection_join2_Entity $joinHasWhere {m:1 DibiCollection_joinHasWhere_}
 * @property DibiCollection_join2_Entity $joinBadCollection {m:1 DibiCollection_joinBadCollection_}
 */
class DibiCollection_join2_Entity extends Entity
{

}

class DibiCollection_join1_Repository extends Repository
{
	protected $entityClassName = 'DibiCollection_join1_Entity';

	protected function createMapper()
	{
		return new DibiCollection_join_Mapper($this);
	}
}

class DibiCollection_join2_Repository extends DibiCollection_join1_Repository
{
	protected $entityClassName = 'DibiCollection_join2_Entity';
}

class DibiCollection_join_Mapper extends DibiMapper
{
	static protected $dibiConnection;
	protected function createConnection()
	{
		if (!isset(self::$dibiConnection))
		{
			self::$dibiConnection = new DibiConnection(array(
				'driver' => 'MockEscapeMysql',
			));
		}
		return self::$dibiConnection;
	}
}

class DibiCollection_joinBadMapper_Repository extends Repository
{
	protected $entityClassName = 'DibiCollection_join2_Entity';
}
class DibiCollection_joinBadMapper_Mapper extends ArrayMapper {}

class DibiCollection_joinDifferentConnection_Repository extends Repository
{
	protected $entityClassName = 'DibiCollection_join2_Entity';
}
class DibiCollection_joinDifferentConnection_Mapper extends DibiCollection_join_Mapper
{
	protected function createConnection()
	{
		$old = self::$dibiConnection = NULL;
		$c = parent::createConnection();
		self::$dibiConnection = $old;
		return $c;
	}
}

class DibiCollection_join3_Repository extends Repository
{
	protected $entityClassName = 'DibiCollection_join2_Entity';
}

class DibiCollection_join3_Mapper extends DibiCollection_join_Mapper
{
	public function findAll()
	{
		return parent::findAll()->findByType('xyz');
	}
}


class DibiCollection_join4_Repository extends Repository
{
	protected $entityClassName = 'DibiCollection_join2_Entity';
}

class DibiCollection_join4_Mapper extends DibiCollection_join_Mapper
{
	public function findAll()
	{
		return parent::findAll()
			->findBy(array('join1->join3->type' => 'aaa'))
			->findBy(array('join1->type' => 'bbb'))
		;
	}
}

class DibiCollection_joinBadCollection_Repository extends Repository
{
	protected $entityClassName = 'DibiCollection_join2_Entity';
}

class DibiCollection_joinBadCollection_Mapper extends DibiCollection_join_Mapper
{
	public function findAll()
	{
		return $this->dataSource($this->getTableName());
	}
}

class DibiCollection_joinHasWhere_Repository extends Repository
{
	protected $entityClassName = 'DibiCollection_join2_Entity';
}

class DibiCollection_joinHasWhere_Mapper extends DibiCollection_join_Mapper
{
	public function findAll()
	{
		return parent::findAll()->where('1=1');
	}
}

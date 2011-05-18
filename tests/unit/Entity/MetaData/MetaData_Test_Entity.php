<?php

use Orm\Entity;
use Orm\MetaData;
use Orm\Repository;
use Orm\ArrayMapper;

class MetaData_Test_Entity extends Entity
{
	public static $count = 0;
	public static $metaData;
	public static function createMetaData($entityClass)
	{
		self::$count++;
		return self::$metaData ? self::$metaData : new MetaData($entityClass);
	}

	public function getGetter() {}
	public function setSetter($v) {}
	public function getGetterSetter() {}
	public function setGetterSetter($v) {}
	public function isGetter() {}
	public function isBool() {}

	public static function enum()
	{
		return array('foo' => 'bar');
	}

	const XXX = 'xxx';
	const YYY = 'yyy';
}


class MetaData_Test2_Entity extends MetaData_Test_Entity {}

class MetaData_Test2Repository extends Repository
{
	protected $entityClassName = 'MetaData_Test2_Entity';
}
class MetaData_Test2Mapper extends ArrayMapper
{
	protected function loadData()
	{
		return array();
	}
}

class MetaData_Test3_Entity extends Entity
{
	public static $mapped;
	public static function createMetaData($entityClass)
	{
		$meta = parent::createMetaData($entityClass);
		$meta->addProperty('many', '')
			->setManyToMany('MetaData_Test4', 'many', self::$mapped)
		;
		return $meta;
	}
}
class MetaData_Test3Repository extends Repository
{
	protected $entityClassName = 'MetaData_Test3_Entity';
}
class MetaData_Test3Mapper extends TestsMapper {}

class MetaData_Test4_Entity extends Entity
{
	public static $mapped;
	public static function createMetaData($entityClass)
	{
		$meta = parent::createMetaData($entityClass);
		$meta->addProperty('many', '')
			->setManyToMany('MetaData_Test3', 'many', self::$mapped)
		;
		return $meta;
	}
}
class MetaData_Test4Repository extends Repository
{
	protected $entityClassName = 'MetaData_Test4_Entity';
}
class MetaData_Test4Mapper extends TestsMapper {}

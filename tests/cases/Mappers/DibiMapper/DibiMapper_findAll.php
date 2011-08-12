<?php

use Orm\DibiMapper;
use Orm\Repository;

class DibiMapper_findAll_DibiMapper extends DibiMapper
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

	public $collectionClass;
	protected function createCollectionClass()
	{
		if ($this->collectionClass) return $this->collectionClass;
		return parent::createCollectionClass();
	}
}

class DibiMapper_findAll_DibiRepository extends Repository
{
	protected $entityClassName = 'TestEntity';
}

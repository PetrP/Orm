<?php

use Orm\DibiMapper;
use Orm\DibiCollection;
use Orm\ArrayCollection;
use Orm\DataSourceCollection;

class DibiMapper_dataSource_DibiMapper extends DibiMapper
{
	public function ds()
	{
		$args = func_get_args();
		return call_user_func_array(array($this, 'dataSource'), $args);
	}

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

class DibiMapper_dataSource_DibiCollection extends DibiCollection
{
}

class DibiMapper_dataSource_ArrayCollection extends ArrayCollection
{
}

class DibiMapper_dataSource_DataSourceCollection extends DataSourceCollection
{
}

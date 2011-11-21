<?php

use Orm\Repository;

class Repository_createPerformanceHelper_Repository  extends Repository
{
	protected $entityClassName = 'TestEntity';
	public static $ph;

	protected function createPerformanceHelper()
	{
		if (self::$ph) return self::$ph;
		return parent::createPerformanceHelper();
	}
}

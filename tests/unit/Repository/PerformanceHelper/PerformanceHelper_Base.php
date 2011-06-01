<?php

use Orm\PerformanceHelper;

class PerformanceHelper_Base_PerformanceHelper extends PerformanceHelper
{
	public static $cache;

	protected function getCache()
	{
		return self::$cache;
	}
}

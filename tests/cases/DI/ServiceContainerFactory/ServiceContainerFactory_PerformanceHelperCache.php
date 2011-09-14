<?php

use Orm\ServiceContainerFactory;

class ServiceContainerFactory_PerformanceHelperCache_ServiceContainerFactory extends ServiceContainerFactory
{
	public $phc;
	protected function getPerformanceHelperCacheFactory()
	{
		if ($this->phc !== NULL) return $this->phc;
		parent::getPerformanceHelperCacheFactory();
	}
}

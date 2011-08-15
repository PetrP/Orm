<?php

use Orm\IServiceContainerFactory;
use Orm\ServiceContainer;

class RepositoryContainer_construct_ServiceContainerFactory implements IServiceContainerFactory
{
	private $container;
	public function getContainer()
	{
		if (!$this->container)
		{
			$this->container = new ServiceContainer;
			$this->container->addService('bubu', 'baf');
		}
		return $this->container;
	}
}

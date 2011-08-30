<?php
use Orm\MapperFactory;
use Orm\IRepository;

class MapperFactory_createMapper_Repository extends TestsRepository {}

class MapperFactory_createMapper_MapperFactory extends MapperFactory
{
	public $mc;
	protected function getMapperClass(IRepository $repository)
	{
		return $this->mc;
	}
}

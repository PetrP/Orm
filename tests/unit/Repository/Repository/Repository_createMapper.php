<?php

use Orm\IMapperFactory;
use Orm\IRepository;

class Repository_createMapper_MapperFactory implements IMapperFactory
{
	public $class;
	public function createMapper(IRepository $repository)
	{
		return $this->class;
	}
}

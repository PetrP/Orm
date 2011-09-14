<?php

use Orm\Repository;

class Repository_getMapper_BadMapper_Repository extends Repository
{
	protected $entityClassName = 'TestEntity';
}

class Repository_getMapper_BadMapper_Mapper extends Directory
{
}

class Repository_getMapper_BadMapper2_Repository extends Repository_getMapper_BadMapper_Repository
{
	protected function createMapper()
	{
		return 'StringMapper';
	}
}

class Repository_DefaultMapper_Repository extends Repository
{
	protected $entityClassName = 'TestEntity';
}

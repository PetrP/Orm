<?php

namespace Repository_createMapper;

use Orm\Repository;
use Orm\ArrayMapper;

class Repository_createMapperRepository extends Repository
{
	public $entityClassName = 'TestEntity';
}

class Repository_createMapperMapper extends ArrayMapper
{

}

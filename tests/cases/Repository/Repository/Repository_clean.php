<?php

use Orm\RepositoryContainer;

class Repository_clean_Model extends RepositoryContainer
{
	public $count = 0;
	public function clean()
	{
		$this->count++;
		parent::clean();
	}
}

class Repository_clean_Repository extends TestsRepository
{
}

class Repository_clean_Mapper extends TestsMapper
{
	public $count = 0;
	public function rollback()
	{
		$this->count++;
		return parent::rollback();
	}
}

class Repository_clean2_Repository extends TestsRepository
{
}

class Repository_clean2_Mapper extends Repository_clean_Mapper
{
}

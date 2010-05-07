<?php

final class SimpleRepository extends Repository
{
	private $name;
	
	public function getRepositoryName()
	{
		return $this->name;
	}
}

class SimpleMapper extends Mapper
{
	
	public function findAll()
	{
		return $this->apply($this->connection->dataSourceX('SELECT * FROM %n' , $this->repository->getRepositoryName()));
	}
	
}

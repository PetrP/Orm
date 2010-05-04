<?php

final class SimpleRepository extends Repository
{
	private $name;
	
	public function getRepositoryName()
	{
		return $this->name;
	}
}

final class SimpleMapper extends Repository
{
	private $name;
	
	
	public function getMapperName()
	{
		return $this->name;
	}
	
}
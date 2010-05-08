<?php

abstract class Repository extends Object implements IRepository
{
	private $mapper;
	
	private $repositoryName;
	
	public function __construct($repositoryName)
	{
		$this->repositoryName = $repositoryName;
	}
	
	protected function getMapper()
	{
		if (!isset($this->mapper))
		{
			$mapper = $this->createMapper();
			if (!($mapper instanceof Mapper))
			{
				throw new InvalidStateException();
			}
			$this->mapper = $mapper;
		}
		return $this->mapper;
	}
	
	protected function createMapper()
	{
		return new SimpleSqlMapper($this);
	}
	
	public function createEntity(StdObject $data)
	{
		$entityName = $this->getRepositoryName();
		//return unserialize("O:".strlen($n).":\"$n\":1:{s:14:\"\0Entity\0params\";".serialize($data->d)."}");
		return call_user_func(array($entityName, 'create'), $entityName, (array) $data);
	}
	
	public function __call($name, $args)
	{
		return call_user_func_array(array($this->getMapper(), $name), $args);
	}
	
	public function getRepositoryName()
	{
		return $this->repositoryName;
	}
	
	public function persist(Entity $e)
	{
		return $this->getMapper()->persist($e);
	}
	
}



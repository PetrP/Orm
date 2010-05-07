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
		$class = ucfirst($this->getRepositoryName()) . 'Mapper';
		return new $class($this);
	}
	
	public function createEntity(array $data)
	{
		$n = $this->getRepositoryName();
		//return unserialize("O:".strlen($n).":\"$n\":1:{s:14:\"\0Entity\0params\";".serialize($data->d)."}");
		return call_user_func(array($n, 'create'), $n, $data->d);
	}
	
	public function __call($name, $args)
	{
		return call_user_func_array(array($this->getMapper(), $name), $args);
	}
	
	public function getRepositoryName()
	{
		return $this->repositoryName;
	}
	
}



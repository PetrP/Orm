<?php

abstract class Repository extends Object implements IRepository
{
	private $mapper;
	
	private $repositoryName;
	protected $conventional;
	
	public function __construct($repositoryName)
	{
		$this->repositoryName = $repositoryName;
		$this->conventional = $this->getMapper()->getConventional(); // speedup
	}
	
	final public function getMapper()
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
		if (class_exists($class))
		{
			return new $class($this);
		}
		return new SimpleSqlMapper($this);
	}
	
	public function getEntityName(StdObject $data = NULL)
	{
		return rtrim($this->getRepositoryName(), 's');
		//return unserialize("O:".strlen($n).":\"$n\":1:{s:14:\"\0Entity\0params\";".serialize($data->d)."}");
		//return call_user_func(array($entityName, 'create'), $entityName, (array) $data);
	}
	
	final public function createEntity(StdObject $data)
	{
		static $e = array();
		if (!isset($e[$data->id]))
		{
			$entityName = $this->getEntityName($data);
			$e[$data->id] = Entity::create($entityName, (array) $this->conventional->unformat($data, $entityName));
		}
		return $e[$data->id];
	}
	
	public function __call($name, $args)
	{
		return call_user_func_array(array($this->getMapper(), $name), $args);
	}
	
	final public function getRepositoryName()
	{
		return $this->repositoryName;
	}
	
	public function persist(Entity $e)
	{
		if (!@is_a($e, $this->getEntityName())) // php 5.0 - 5.2 throw deprecated
		{
			throw new UnexpectedValueException();
		}
		return $this->getMapper()->persist($e);
	}
	
}



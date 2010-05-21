<?php

abstract class Repository extends Object implements IRepository
{
	private $mapper;

	private $repositoryName;
	protected $conventional;

	private $entities = array();

	public function getById($id)
	{
		if ($id instanceof Entity)
		{
			$id = $id->id;
		}
		if (isset($this->entities[$id]))
		{
			return $this->entities[$id];
		}
		return $this->getMapper()->getById($id);
	}

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

	public function getEntityName(array $data = NULL)
	{
		return rtrim($this->getRepositoryName(), 's');
		//return unserialize("O:".strlen($n).":\"$n\":1:{s:14:\"\0Entity\0params\";".serialize($data->d)."}");
		//return call_user_func(array($entityName, 'create'), $entityName, (array) $data);
	}

	final public function createEntity($data)
	{
		if (!isset($this->entities[$data['id']]))
		{
			$originData = $data;
			$defaultEntity = $this->getEntityName();
			$data = (array) $this->conventional->unformat($data, $defaultEntity);
			$entityName = $this->getEntityName($data);
			if ($defaultEntity !== $entityName)
			{
				$data = (array) $this->conventional->unformat($originData, $entityName);
			}
			$this->entities[$data['id']] = Entity::create($entityName, $data);
		}
		return $this->entities[$data['id']];
	}

	public function __call($name, $args)
	{
		return call_user_func_array(array($this->getMapper(), $name), $args);
	}

	final public function getRepositoryName()
	{
		return $this->repositoryName;
	}

	public function persist(Entity $entity, $beAtomic = true)
	{
		if (!@is_a($entity, $this->getEntityName())) // php 5.0 - 5.2 throw deprecated
		{
			throw new UnexpectedValueException();
		}
		return $this->getMapper()->persist($entity, $beAtomic);
	}

	public function delete($entity, $beAtomic = true)
	{
		if ($entity instanceof Entity AND !@is_a($entity, $this->getEntityName())) // php 5.0 - 5.2 throw deprecated
		{
			throw new UnexpectedValueException();
		}
		return $this->getMapper()->delete($entity, $beAtomic);
	}

}



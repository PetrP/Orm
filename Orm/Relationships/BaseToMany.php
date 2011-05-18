<?php

abstract class BaseToMany extends Object
{

	/** @var IRepository|string */
	private $repository;

	/**
	 * @param array of IEntity|scalar|array
	 * @return IRelationship $this
	 */
	final public function set(array $data)
	{
		foreach ($this->get() as $entity)
		{
			$this->remove($entity);
		}
		foreach ($data as $row)
		{
			if ($row === NULL) continue;
			$this->add($row);
		}
		return $this;
	}

	/**
	 * @param IRepository|string repositoryName for lazy load
	 */
	public function __construct($repository)
	{
		$this->repository = $repository;
	}

	/** @return int */
	public function count()
	{
		return $this->get()->count();
	}

	/** @return Traversable */
	public function getIterator()
	{
		return $this->get()->getIterator();
	}

	public function getInjectedValue()
	{
		return NULL;
	}

	/** @param array|NULL */
	public function setInjectedValue($value)
	{
		if ($value !== NULL) $this->set($value);
	}

	/**
	 * Repository
	 * @return Repository
	 */
	protected function getChildRepository()
	{
		if (!($this->repository instanceof IRepository))
		{
			$this->repository = $this->getModel()->getRepository($this->repository);
		}
		return $this->repository;
	}

	/**
	 * Vytvori / nacte / vrati entitu.
	 * @param IEntity|scalar|array
	 * @return IEntity
	 */
	protected function createEntity($entity)
	{
		$repository = $this->getChildRepository(); // todo neni mozne kdyz parent neni pripojen na repo
		if (!($entity instanceof IEntity) AND (is_array($entity) OR $entity instanceof Traversable))
		{
			$array = $entity instanceof Traversable ? iterator_to_array($entity) : $entity;
			$entity = NULL;
			if (isset($array['id']))
			{
				$entity = $repository->getById($array['id']);
			}
			if (!$entity)
			{
				$entityName = $repository->getEntityClassName($array);
				$entity = new $entityName; // todo construct pak nesmy mit povine parametry
				$repository->attach($entity);
			}
			$entity->setValues($array);
		}
		if (!($entity instanceof IEntity))
		{
			$id = $entity;
			$entity = $repository->getById($id);
			if (!$entity)
			{
				throw new UnexpectedValueException("Entity '$id' not found in `" . get_class($repository) . "`");
			}
		}
		$repository->attach($entity);
		return $entity;
	}

	protected function ignore(IEntity $entity)
	{
		return false;
	}

}

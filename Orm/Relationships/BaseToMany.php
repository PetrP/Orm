<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use Nette\Object;
use Traversable;

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
		foreach ($this->getCollection() as $entity)
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

	/** @return IEntityCollection */
	final public function get()
	{
		return $this->getCollection()->toCollection();
	}

	/** @param IRepository|string repositoryName for lazy load */
	public function __construct($repository)
	{
		$this->repository = $repository;
	}

	/** @return int */
	final public function count()
	{
		return $this->getCollection()->count();
	}

	/** @return Traversable */
	final public function getIterator()
	{
		return $this->getCollection()->getIterator();
	}

	/** @return NULL */
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
	 * @param bool
	 * @return Repository|NULL
	 */
	protected function getChildRepository($need = true)
	{
		if (!($this->repository instanceof IRepository))
		{
			if (!($model = $this->getModel($need)))
			{
				return NULL;
			}
			$this->repository = $model->getRepository($this->repository);
		}
		return $this->repository;
	}

	/**
	 * Vytvori / nacte / vrati entitu.
	 * @param IEntity|scalar|array
	 * @param bool
	 * @return IEntity|NULL null only if not invasive
	 * @throws EntityNotFoundException
	 */
	protected function createEntity($entity, $invasive = true)
	{
		$repository = $this->getChildRepository($invasive); // todo neni mozne kdyz parent neni pripojen na repo
		if (!$repository)
		{
			return NULL;
		}
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
				if (!$invasive)
				{
					return NULL;
				}
				$entityName = $repository->getEntityClassName($array);
				$entity = new $entityName; // todo construct pak nesmy mit povine parametry
				$repository->attach($entity);
			}
			if ($invasive)
			{
				$entity->setValues($array);
			}
		}
		if (!($entity instanceof IEntity))
		{
			$id = $entity;
			$entity = $repository->getById($id);
			if (!$entity)
			{
				if (!$invasive)
				{
					return NULL;
				}
				throw new EntityNotFoundException("Entity '$id' not found in `" . get_class($repository) . "`");
			}
		}
		if ($invasive)
		{
			$repository->attach($entity);
		}
		else if (!$repository->isAttachableEntity($entity))
		{
			return NULL;
		}
		return $entity;
	}

	/**
	 * Kdyz true tak se entita pri add tise ignoruje.
	 * @param IEntity
	 * @return bool
	 */
	protected function ignore(IEntity $entity)
	{
		return false;
	}

	/**
	 * @return IEntityCollection
	 * @see self::get()
	 */
	abstract protected function getCollection();

}

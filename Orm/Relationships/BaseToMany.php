<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use Traversable;

/**
 * Common things for OneToMany and ManyToMany.
 * @author Petr Procházka
 * @package Orm
 * @subpackage Relationships
 */
abstract class BaseToMany extends Object
{
	/** @var IEntity */
	private $parent;

	/** @var RelationshipMetaDataToMany */
	private $metaData;

	/** @var IRepository cache */
	private $childRepository;

	/**
	 * @param IEntity
	 * @param RelationshipMetaDataToMany
	 */
	public function __construct(IEntity $parent, RelationshipMetaDataToMany $metaData)
	{
		$this->parent = $parent;
		$this->metaData = $metaData;
	}

	/**
	 * Substitute all Entity with given ones.
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

	/**
	 * Returns collection of all entity.
	 * @return IEntityCollection
	 */
	final public function get()
	{
		return $this->getCollection()->toCollection();
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
	 * @param bool
	 * @return IRepositoryContainer
	 * @todo deprecated?
	 */
	public function getModel($need = true)
	{
		return $this->parent->getModel((bool) $need);
	}

	/**
	 * Repository
	 * @param bool
	 * @return Repository|NULL
	 */
	protected function getChildRepository($need = true)
	{
		if (!$this->childRepository)
		{
			if (!($model = $this->getModel($need)))
			{
				return NULL;
			}
			$this->childRepository = $model->getRepository($this->metaData->getChildRepository());
		}
		return $this->childRepository;
	}

	/** @return IEntity */
	final protected function getParent()
	{
		return $this->parent;
	}

	/** @return RelationshipMetaDataToMany */
	final protected function getMetaData()
	{
		return $this->metaData;
	}

	/**
	 * Vytvori / nacte / vrati entitu.
	 * @param IEntity|scalar|array
	 * @param bool
	 * @return IEntity|NULL null only if not invasive
	 * @throws EntityNotFoundException
	 */
	final protected function createEntity($entity, $invasive = true)
	{
		if ($entity instanceof IEntity)
		{
			$model = $entity->getModel(false);
			if ($model AND $this->parent->getModel(false) !== $model)
			{
				$this->parent->fireEvent('onAttachModel', NULL, $model);
			}
		}
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
	 * Kdyz false tak se entita pri add vyhodí chybu.
	 * @param IEntity
	 * @return bool
	 */
	protected function check(IEntity $entity)
	{
		return true;
	}

	/**
	 * @param IEntity
	 * @return bool
	 * @throws BadEntityException
	 */
	final protected function handleCheckAndIgnore(IEntity $entity)
	{
		if ($this->ignore($entity))
		{
			return true;
		}
		if (!$this->check($entity))
		{
			throw new BadEntityException(get_class($this) . '::check() ' . EntityHelper::toString($entity) . ' is not allowed for that relationship.');
		}
		return false;
	}

	/**
	 * @return IEntityCollection
	 * @see self::get()
	 */
	abstract protected function getCollection();

}

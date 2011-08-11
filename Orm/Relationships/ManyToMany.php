<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use Nette\InvalidStateException;

require_once __DIR__ . '/IRelationship.php';
require_once __DIR__ . '/BaseToMany.php';
require_once __DIR__ . '/DibiManyToManyMapper.php';
require_once __DIR__ . '/ArrayManyToManyMapper.php';

// todo rict parent entity ze se zmenila
class ManyToMany extends BaseToMany implements IRelationship
{
	/** @var Entity */
	private $parent;

	/** @var string */
	private $param;

	/** @var IEntityCollection @see self::getCollection() */
	private $get;

	/**
	 * Pridane entity
	 * @var array of IEntity
	 * @see self::add()
	 */
	private $add = array();

	/**
	 * Odebrane z kolekce.
	 * @var array of IEntity
	 * @see self::remove()
	 */
	private $del = array();

	/**
	 * @see self::getMapper()
	 * @var IManyToManyMapper
	 */
	private $mapper;

	/**
	 * @see self::getMapper()
	 * @see ArrayManyToManyMapper::setInjectedValue()
	 * @var mixed
	 */
	private $initialValue;

	/** @var bool */
	private $mappedByParent;

	/** @var string */
	private $parentParam;

	/**
	 * @param IEntity
	 * @param IRepository|string repositoryName for lazy load
	 * @param string m:1 param on child entity
	 * @param string m:1 param on parent entity
	 * @param bool
	 * @param mixed
	 */
	public function __construct(IEntity $parent, $repository, $childParam, $parentParam, $mappedByParent, $value = NULL)
	{
		$this->parent = $parent;
		$this->parentParam = $parentParam;
		$this->param = $childParam;
		$this->mappedByParent = $mappedByParent;
		$this->initialValue = $value;
		parent::__construct($repository);
	}

	/**
	 * @param IEntity|scalar|array
	 * @return IEntity|NULL
	 */
	final public function add($entity)
	{
		$entity = $this->createEntity($entity);
		if ($this->ignore($entity)) return NULL;
		// $entity->manytomany->add($this->parent); // todo kdyz existuje?
		$hash = spl_object_hash($entity);
		$this->add[$hash] = $entity;
		return $entity;
	}

	/**
	 * @param IEntity|scalar|array
	 * @return IEntity
	 */
	final public function remove($entity)
	{
		$entity = $this->createEntity($entity);
		// $entity->manytomany->remove($this->parent); // todo kdyz existuje?
		$hash = spl_object_hash($entity);
		if (isset($this->add[$hash]))
		{
			unset($this->add[$hash]);
		}
		else
		{
			$this->del[$hash] = $entity;
		}
		return $entity;
	}

	/**
	 * @param IEntity|scalar|array
	 * @return bool
	 */
	final public function has($entity)
	{
		if ($entity = $this->createEntity($entity, false))
		{
			$hash = spl_object_hash($entity);
			if (isset($this->add[$hash]))
			{
				return true;
			}
			if (isset($this->del[$hash]))
			{
				return false;
			}
			if (isset($entity->id))
			{
				return (bool) $this->getCollection()->getById($entity->id);
			}
		}
		return false;
	}

	/** @return IEntityCollection */
	final protected function getCollection()
	{
		if (!isset($this->get))
		{
			$ids = $this->getMapper()->load($this->parent);
			$all = $ids ? $this->getChildRepository()->mapper->findById($ids) : new ArrayCollection(array());
			if ($this->add OR $this->del)
			{
				$array = array();
				foreach ($all as $entity)
				{
					$array[spl_object_hash($entity)] = $entity;
				}
				foreach ($this->del as $hash => $entity)
				{
					unset($array[$hash]);
				}
				foreach ($this->add as $hash => $entity)
				{
					unset($array[$hash]);
					$array[$hash] = $entity;
				}
				$all = new ArrayCollection($array);
			}
			$this->get = $all;
		}
		return $this->get;
	}

	/** @see IManyToManyMapper */
	final public function persist()
	{
		$repository = $this->getChildRepository();

		$del = $add = array();

		foreach ($this->del as $entity)
		{
			//$repository->remove($entity);
			if (isset($entity->id)) $del[$entity->id] = $entity->id;
		}

		if ($this->get)
		{
			foreach ($this->get as $entity)
			{
				$repository->persist($entity);
			}
		}
		foreach ($this->add as $entity)
		{
			$repository->persist($entity);
			$add[$entity->id] = $entity->id;
		}

		if ($del) $this->getMapper()->remove($this->parent, $del);
		if ($add) $this->getMapper()->add($this->parent, $add);

		$this->del = $this->add = array();
		if ($this->get instanceof ArrayCollection) $this->get = NULL; // free memory
	}

	/**
	 * @param bool
	 * @return IRepositoryContainer
	 */
	public function getModel($need = true)
	{
		return $this->parent->getModel((bool) $need);
	}

	/** @return mixed */
	public function getInjectedValue()
	{
		$mapper = $this->getMapper();
		if ($mapper instanceof IEntityInjection)
		{
			return $mapper->getInjectedValue();
		}
	}

	/** @return bool */
	final public function isMappedByParent()
	{
		return $this->mappedByParent;
	}

	/**
	 * @return IManyToManyMapper
	 */
	protected function getMapper()
	{
		if (!isset($this->mapper))
		{
			$parentRepository = $this->parent->getRepository(false);
			$childRepository = $this->getChildRepository(false);
			if ($parentRepository AND $childRepository)
			{
				if ($this->mappedByParent)
				{
					$mapper = $parentRepository->getMapper()->createManyToManyMapper($this->parentParam, $childRepository, $this->param);
				}
				else
				{
					$mapper = $childRepository->getMapper()->createManyToManyMapper($this->param, $parentRepository, $this->parentParam);
				}
				if (!($mapper instanceof IManyToManyMapper))
				{
					$tmp = $this->mappedByParent ? $parentRepository->getMapper() : $childRepository->getMapper();
					throw new InvalidStateException(get_class($tmp) . "::createManyToManyMapper() must return Orm\\IManyToManyMapper, '" . (is_object($mapper) ? get_class($mapper) : gettype($mapper)) . "' given");
				}
				$mapper->attach($this);

				if ($mapper instanceof IEntityInjection)
				{
					$mapper->setInjectedValue($this->initialValue);
				}
				$this->mapper = $mapper;
			}
			else
			{
				return new ArrayManyToManyMapper;
			}
		}
		return $this->mapper;
	}

	/**
	 * Vytvori / nacte / vrati entitu.
	 * if invasive: Vyprazdni get.
	 * @param IEntity|scalar|array
	 * @param bool
	 * @return IEntity|NULL null only if not invasive
	 */
	final protected function createEntity($entity, $invasive = true)
	{
		$entity = parent::createEntity($entity, $invasive);
		if ($invasive)
		{
			$this->get = NULL;
		}
		return $entity;
	}

}

require_once __DIR__ . '/bcmm.php';

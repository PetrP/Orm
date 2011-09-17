<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * ManyToMany relationship.
 *
 * <code>
 *
 * /**
 *  * @property Orm\ManyToMany $bars {m:m BarsRepository foos map}
 *  * /
 * class Foo extends Orm\Entity {}
 *
 * /**
 *  * @property Orm\ManyToMany $foos {m:m FoosRepository bars}
 *  * /
 * class Bar extends Orm\Entity {}
 *
 * $foo->bars->add(new Bar);
 * $foo->bars->add(123);
 * $foo->bars->add(array('name' => 'xyz'));
 *
 * foreach ($foo->bars as $bar) {}
 *
 * $foo->bars->get()->getByName('xyz');
 *
 * $foo->bars->set(array(new Bar, new Bar));
 *
 * </code>
 * @author Petr Procházka
 * @package Orm
 * @subpackage Relationships
 */
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

	/** @var RelationshipLoader::MAPPED_* */
	private $mapped;

	/** @var string */
	private $parentParam;

	/**
	 * @param IEntity
	 * @param IRepository|string repositoryName for lazy load
	 * @param string m:1 param on child entity
	 * @param string m:1 param on parent entity
	 * @param mixed RelationshipLoader::MAPPED_*
	 * @param mixed
	 */
	public function __construct(IEntity $parent, $repository, $childParam, $parentParam, $mapped, $value = NULL)
	{
		$this->parent = $parent;
		$this->parentParam = $parentParam;
		$this->param = $childParam;
		$this->mapped = $mapped;
		$this->initialValue = $value;
		parent::__construct($repository);
	}

	/**
	 * @param IEntity|scalar|array
	 * @return IEntity|NULL
	 * @throws BadEntityException
	 */
	final public function add($entity)
	{
		$entity = $this->createEntity($entity);
		if ($this->handleCheckAndIgnore($entity)) return NULL;
		// $entity->manytomany->add($this->parent); // todo kdyz existuje?
		$this->parent->markAsChanged($this->parentParam);
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
		$this->parent->markAsChanged($this->parentParam);
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
		if ($this->get === NULL)
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

	/**
	 * @see IManyToManyMapper
	 * @return void
	 */
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

	/** @return mixed RelationshipLoader::MAPPED_* */
	final public function getWhereIsMapped()
	{
		return $this->mapped;
	}

	/**
	 * @deprecated
	 * @return bool
	 */
	final public function isMappedByParent()
	{
		return $this->mapped === RelationshipLoader::MAPPED_HERE OR $this->mapped === RelationshipLoader::MAPPED_BOTH;
	}

	/**
	 * @return IManyToManyMapper
	 */
	protected function getMapper()
	{
		if ($this->mapper === NULL)
		{
			$parentRepository = $this->parent->getRepository(false);
			$childRepository = $this->getChildRepository(false);
			if ($parentRepository AND $childRepository)
			{
				if ($this->mapped === RelationshipLoader::MAPPED_HERE OR $this->mapped === RelationshipLoader::MAPPED_BOTH)
				{
					$repoMapper = $parentRepository->getMapper();
					$mapper = $repoMapper->createManyToManyMapper($this->parentParam, $childRepository, $this->param);
				}
				else if ($this->mapped === RelationshipLoader::MAPPED_THERE)
				{
					$repoMapper = $childRepository->getMapper();
					$mapper = $repoMapper->createManyToManyMapper($this->param, $parentRepository, $this->parentParam);
				}
				else
				{
					throw new InvalidArgumentException(array(__CLASS__, 'mapped', 'Orm\RelationshipLoader::MAPPED_HERE, MAPPED_THERE or MAPPED_BOTH', $this->mapped));
				}
				if (!($mapper instanceof IManyToManyMapper))
				{
					throw new BadReturnException(array($repoMapper, 'createManyToManyMapper', 'Orm\IManyToManyMapper', $mapper));
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

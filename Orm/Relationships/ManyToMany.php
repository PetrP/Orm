<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use Nette\InvalidStateException;
use Nette\InvalidArgumentException;

require_once __DIR__ . '/IRelationship.php';
require_once __DIR__ . '/BaseToMany.php';
require_once __DIR__ . '/DibiManyToManyMapper.php';
require_once __DIR__ . '/ArrayManyToManyMapper.php';

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
	 */
	final public function add($entity)
	{
		$entity = $this->createEntity($entity);
		if ($this->ignore($entity)) return NULL;
		// $entity->manytomany->add($this->parent); // todo kdyz existuje?
		$this->parent->isChanged(true);
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
		$this->parent->isChanged(true);
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
	public function persist()
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

	/** @return IRepositoryContainer */
	public function getModel()
	{
		return $this->parent->getModel();
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
			if ($this->parent->getModel(false))
			{
				$parentRepository = $this->parent->getGeneratingRepository();
				$childRepository = $this->getChildRepository();
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
					throw new InvalidArgumentException('Orm\ManyToMany::mapped must be Orm\RelationshipLoader::MAPPED_HERE, MAPPED_THERE or MAPPED_BOTH.');
				}
				if (!($mapper instanceof IManyToManyMapper))
				{
					throw new InvalidStateException(get_class($repoMapper) . "::createManyToManyMapper() must return Orm\\IManyToManyMapper, '" . (is_object($mapper) ? get_class($mapper) : gettype($mapper)) . "' given");
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
	 * Vyprazdni get.
	 * @param IEntity|scalar|array
	 * @return IEntity
	 */
	final protected function createEntity($entity)
	{
		$entity = parent::createEntity($entity);
		$this->get = NULL;
		return $entity;
	}

}

require_once __DIR__ . '/bcmm.php';

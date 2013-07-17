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
	 * @see ArrayManyToManyMapper::getInjectedValue()
	 * @var array
	 */
	private $injectedValue;

	/**
	 * @param IEntity
	 * @param RelationshipMetaDataManyToMany
	 * @param mixed
	 */
	public function __construct(IEntity $parent, RelationshipMetaDataManyToMany $metaData, $value = NULL)
	{
		$this->injectedValue = $value;
		parent::__construct($parent, $metaData);
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
		$meta = $this->getMetaData();
		$parent = $this->getParent();
		if ($childParam = $meta->getChildParam())
		{
			$childManyToMany = $entity->{$childParam};
			if (!($childManyToMany instanceof self))
			{
				throw new NotValidException(array($entity, $childParam, 'instanceof ' . __CLASS__, $childManyToMany));
			}
			if ($childManyToMany->handleCheckAndIgnore($parent)) return NULL;
		}
		$parent->markAsChanged($meta->getParentParam());
		$this->add[spl_object_hash($entity)] = $entity;
		$this->get = NULL;
		if ($childParam)
		{
			$entity->markAsChanged($childParam);
			$childManyToMany->add[spl_object_hash($parent)] = $parent;
			$childManyToMany->get = NULL;
		}
		return $entity;
	}

	/**
	 * @param IEntity|scalar|array
	 * @return IEntity
	 */
	final public function remove($entity)
	{
		$entity = $this->createEntity($entity);
		$meta = $this->getMetaData();
		$parent = $this->getParent();
		if ($childParam = $meta->getChildParam())
		{
			$childManyToMany = $entity->{$childParam};
			if (!($childManyToMany instanceof self))
			{
				throw new NotValidException(array($entity, $childParam, 'instanceof ' . __CLASS__, $childManyToMany));
			}
		}
		$parent->markAsChanged($meta->getParentParam());
		$hash = spl_object_hash($entity);
		if (isset($this->add[$hash]))
		{
			unset($this->add[$hash]);
		}
		else
		{
			$this->del[$hash] = $entity;
		}
		$this->get = NULL;
		if ($childParam)
		{
			$entity->markAsChanged($childParam);
			$hash = spl_object_hash($parent);
			if (isset($childManyToMany->add[$hash]))
			{
				unset($childManyToMany->add[$hash]);
			}
			else
			{
				$childManyToMany->del[$hash] = $parent;
			}
			$childManyToMany->get = NULL;
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

	/**
	 * Loads collection of entities for this association.
	 * @param IRepository
	 * @param array of scalar
	 * @return IEntityCollection
	 */
	protected function loadCollection(IRepository $repository, array $ids)
	{
		if (!$ids)
		{
			return new ArrayCollection(array());
		}
		return $repository->getMapper()->findById($ids);
	}

	/**
	 * @return IEntityCollection
	 * @see self::loadCollection()
	 */
	final protected function getCollection()
	{
		if ($this->get === NULL)
		{
			$parent = $this->getParent();
			if ($this->getModel(false) AND isset($parent->id))
			{
				$repository = $this->getChildRepository(false);
				$ids = $this->getMapper()->load($parent, $this->injectedValue);
				$all = $this->loadCollection($repository, $ids);
				if (!($all instanceof IEntityCollection))
				{
					throw new BadReturnException(array($this, 'loadCollection', 'Orm\IEntityCollection', $all));
				}
			}
			else
			{
				$all = new ArrayCollection(array());
			}
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
	 * @param bool Persist all associations?
	 * @return void
	 */
	final public function persist($all = true)
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
				if ($all OR !isset($entity->id))
				{
					$repository->persist($entity, $all);
				}
			}
		}
		foreach ($this->add as $entity)
		{
			if ($all OR !isset($entity->id))
			{
				$repository->persist($entity, $all);
			}
			$add[$entity->id] = $entity->id;
		}

		if ($this->getMetaData()->getWhereIsMapped() !== RelationshipMetaDataToMany::MAPPED_THERE)
		{
			if ($del) $this->injectedValue = $this->getMapper()->remove($this->getParent(), $del, $this->injectedValue);
			if ($add) $this->injectedValue = $this->getMapper()->add($this->getParent(), $add, $this->injectedValue);
		}

		$this->del = $this->add = array();
		if ($this->get instanceof ArrayCollection) $this->get = NULL; // free memory
	}

	/** @return mixed */
	public function getInjectedValue()
	{
		if ($this->getMapper())
		{
			return $this->injectedValue;
		}
	}

	/**
	 * @return IManyToManyMapper|NULL
	 */
	protected function getMapper()
	{
		if ($this->mapper === NULL AND $parentRepository = $this->getParent()->getRepository(false))
		{
			$mapper = $this->getMetaData()->getMapper($parentRepository);
			$this->injectedValue = $mapper->validateInjectedValue($this->injectedValue);
			$this->mapper = $mapper;
		}
		return $this->mapper;
	}

	/**
	 * @deprecated
	 * @return mixed RelationshipMetaDataToMany::MAPPED_*
	 */
	final public function getWhereIsMapped()
	{
		return $this->getMetaData()->getWhereIsMapped();
	}

	/**
	 * @deprecated
	 * @return bool
	 */
	final public function isMappedByParent()
	{
		$mapped = $this->getMetaData()->getWhereIsMapped();
		return $mapped === RelationshipMetaDataToMany::MAPPED_HERE OR $mapped === RelationshipMetaDataToMany::MAPPED_BOTH;
	}

}

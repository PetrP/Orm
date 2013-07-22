<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use Exception;

/**
 * OneToMany relationship.
 *
 * <code>
 *
 * /**
 *  * @property Orm\OneToMany $bars {1:m BarsRepository foo}
 *  * /
 * class Foo extends Orm\Entity {}
 *
 * /**
 *  * @property Foo $foo {m:1 FoosRepository bars}
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
class OneToMany extends BaseToMany implements IRelationship
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
	 * Upravene entity, tzn odebrane z kolekce.
	 * @var array of IEntity
	 * @see self::remove()
	 */
	private $edit = array();

	/**
	 * Smazane entity, tzn odebrane z kolekce.
	 * @var array of IEntity
	 * @see self::remove()
	 */
	private $del = array();

	/**
	 * @deprecated Default ordering by property order is deprecated and in future version will be removed.
	 * @var string|NULL|false
	 * @see self::getOrderProperty()
	 */
	private $defaltOrderPropertyBCValue = false;

	/**
	 * @param IEntity
	 * @param RelationshipMetaDataOneToMany
	 */
	public function __construct(IEntity $parent, RelationshipMetaDataOneToMany $metaData)
	{
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
		$param = $meta->getChildParam();
		$parent = $this->getParent();
		$parentParam = $meta->getParentParam();
		$hash = spl_object_hash($entity);
		if (isset($entity->$param) AND ($paramValue = $entity->__get($param)) !== NULL AND $paramValue !== $parent)
		{
			$oldParentOneToMany = isset($paramValue->{$parentParam}) ? $paramValue->__get($parentParam) : NULL;
			if (!($oldParentOneToMany instanceof self))
			{
				throw new NotValidException(array($entity, $param . '::$' . $parentParam, 'instanceof ' . __CLASS__, $oldParentOneToMany));
			}
			if (!isset($oldParentOneToMany->del[$hash])) // znamena ze byla odstranena, ale $entity->$param nedokaze byt null
			{
				throw new InvalidEntityException('Entity '. EntityHelper::toString($entity) . ' is already asociated with another entity.');
			}
		}
		$parent->markAsChanged($parentParam);
		$entity->__set($param, $parent);
		unset($this->edit[$hash], $this->del[$hash]);
		$this->add[$hash] = $entity;
		$this->get = NULL;
		return $entity;
	}

	/**
	 * @param IEntity|scalar|array
	 * @return IEntity
	 */
	final public function remove($entity)
	{
		$entity = $this->createEntity($entity);
		$param = $this->getMetaData()->getChildParam();
		$parent = $this->getParent();
		$hash = spl_object_hash($entity);
		if (!isset($entity->$param) OR $entity->$param !== $parent)
		{
			if (func_num_args() >= 2 AND (($internalParameter = func_get_arg(1)) === 'handled by ManyToOne' OR $internalParameter === 'handled by ManyToOne remove')) // :-) internal
			{
				$parent->markAsChanged($this->getMetaData()->getParentParam());
				unset($this->add[$hash], $this->edit[$hash], $this->del[$hash]);
				if ($internalParameter !== 'handled by ManyToOne remove')
				{
					$this->edit[$hash] = $entity;
				}
				$this->get = NULL;
				return $entity;
			}
			throw new InvalidEntityException('Entity '. EntityHelper::toString($entity) . ' is not asociated with this entity.');
		}
		$parent->markAsChanged($this->getMetaData()->getParentParam());
		unset($this->add[$hash], $this->edit[$hash], $this->del[$hash]);
		try {
			$entity->__set($param, NULL);
			$this->edit[$hash] = $entity;
		} catch (Exception $e) {
			$this->del[$hash] = $entity;
			// todo wtf chovani, kdyz nemuze existovat bez param tak se vymaze
		}
		$this->get = NULL;
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
			if (isset($this->del[spl_object_hash($entity)]))
			{
				return false;
			}
			$param = $this->getMetaData()->getChildParam();
			return isset($entity->$param) AND $entity->$param === $this->getParent();
		}
		return false;
	}

	/**
	 * Loads collection of entities for this association.
	 * @param IRepository
	 * @param IEntity
	 * @return string
	 */
	protected function loadCollection(IRepository $repository, IEntity $parent, $param)
	{
		$method = 'findBy' . $param;
		return method_exists($repository, $method) ? $repository->$method($parent) : $repository->getMapper()->$method($parent);
	}

	/**
	 * @return IEntityCollection
	 * @see self::loadCollection()
	 */
	final protected function getCollection()
	{
		if ($this->get === NULL)
		{
			$param = $this->getMetaData()->getChildParam();
			$parent = $this->getParent();
			if ($repository = $this->getChildRepository(false) AND isset($parent->id))
			{
				$all = $this->loadCollection($repository, $parent, $param);
				if (!($all instanceof IEntityCollection))
				{
					throw new BadReturnException(array($this, 'loadCollection', 'Orm\IEntityCollection', $all));
				}
				$orderProperty = $this->getOrderProperty();
				if ($orderProperty !== NULL)
				{
					$all = $all->toCollection()->orderBy($orderProperty);
				}
			}
			else
			{
				// parent entity is not handled by repository
				$all = new ArrayCollection(array());
			}
			if ($this->add OR $this->del OR $this->edit)
			{
				$array = array();
				foreach ($all as $entity)
				{
					if (isset($entity->{$param}) AND $entity->{$param} === $parent)
					{
						// zkontroluje data nad uz vytvorenejma entitama, protoze ty entity v edit muzou mit parent = NULL
						$array[spl_object_hash($entity)] = $entity;
					}
				}
				foreach ($this->add as $hash => $entity)
				{
					if (isset($entity->{$param}) AND $entity->{$param} === $parent)
					{
						unset($array[$hash]);
						$array[$hash] = $entity;
					}
				}
				foreach ($this->del as $hash => $entity)
				{
					unset($array[$hash]);
				}
				$all = new ArrayCollection($array);
			}
			$this->get = $all;
		}
		return $this->get;
	}

	/**
	 * @param bool Persist all associations?
	 * @return void
	 */
	final public function persist($all = true)
	{
		$repository = $this->getChildRepository();

		if ($this->del)
		{
			$param = $this->getMetaData()->getChildParam();
			$parent = $this->getParent();
			foreach ($this->del as $entity)
			{
				if (!isset($entity->{$param}) OR $entity->{$param} === $parent)
				{
					$repository->remove($entity);
				}
				else
				{
					if ($all OR !isset($entity->id))
					{
						$repository->persist($entity, $all);
					}
				}
			}
		}


		if ($this->get)
		{
			$order = 0;
			foreach ($this->get as $entity)
			{
				$this->applyOrderValue($order, $entity);
				if ($all OR !isset($entity->id))
				{
					$repository->persist($entity, $all);
				}
			}
		}
		else
		{
			foreach ($this->edit as $entity)
			{
				if ($all OR !isset($entity->id))
				{
					$repository->persist($entity, $all);
				}
			}
			$order = NULL;
			foreach ($this->add as $entity)
			{
				$this->applyOrderValue($order, $entity);
				if ($all OR !isset($entity->id))
				{
					$repository->persist($entity, $all);
				}
			}
		}

		$this->del = $this->edit = $this->add = array();
		if ($this->get instanceof ArrayCollection) $this->get = NULL; // free memory
	}

	/**
	 * @param int|NULL with reference
	 * @param IEntity
	 */
	protected function applyOrderValue(& $order, IEntity $entity)
	{
		$orderProperty = $this->getOrderProperty();
		if ($orderProperty !== NULL)
		{
			if ($order === NULL)
			{
				$order = $this->loadCollection($this->getChildRepository(), $this->getParent(), $this->getMetaData()->getChildParam())->count();
			}
			$order++;
			if (!isset($entity->{$orderProperty}) OR $entity->{$orderProperty} !== $order)
			{
				$entity->{$orderProperty} = $order;
			}
		}
	}

	/**
	 * @return string|NULL null mean disable
	 */
	protected function getOrderProperty()
	{
		// Default ordering by property order is deprecated and in future version will be removed.
		if ($this->defaltOrderPropertyBCValue === false)
		{
			foreach ($this->getMetaData()->getCanConnectWithEntities($this->getModel()) as $entityClass)
			{
				$meta = MetaData::getEntityRules($entityClass, $this->getModel());
				if (!isset($meta['order']))
				{
					$this->defaltOrderPropertyBCValue = NULL;
					break;
				}
				$this->defaltOrderPropertyBCValue = 'order';
			}
		}

		return $this->defaltOrderPropertyBCValue;
	}

}

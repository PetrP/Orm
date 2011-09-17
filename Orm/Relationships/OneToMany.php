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
 *  * @property Foo $foo {m:1 FoosRepository}
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
	/** @var Entity */
	private $parent;

	/** @var string */
	private $param;

	/** @var string */
	private $parentParam;

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
	 * @param IEntity
	 * @param IRepository|string repositoryName for lazy load
	 * @param string m:1 param on child entity
	 * @param string m:1 param on parent entity
	 */
	public function __construct(IEntity $parent, $repository, $param, $parentParam)
	{
		$this->parent = $parent;
		$this->param = $param;
		$this->parentParam = $parentParam;
		parent::__construct($repository);
	}

	/**
	 * @param IEntity|scalar|array
	 * @return IEntity|NULL
	 * @throws BadEntityException
	 */
	final public function add($entity)
	{
		$param = $this->param;
		$entity = $this->createEntity($entity);
		if ($this->handleCheckAndIgnore($entity)) return NULL;
		if (isset($entity->$param) AND $entity->$param !== NULL AND $entity->$param !== $this->parent)
		{
			throw new InvalidEntityException('Entity '. EntityHelper::toString($entity) . ' is already asociated with another entity.');
		}
		$this->parent->markAsChanged($this->parentParam);
		$entity->$param = $this->parent;
		$this->add[spl_object_hash($entity)] = $entity;
		return $entity;
	}

	/**
	 * @param IEntity|scalar|array
	 * @return IEntity
	 */
	final public function remove($entity)
	{
		$param = $this->param;
		$entity = $this->createEntity($entity);
		if (!isset($entity->$param) OR $entity->$param !== $this->parent)
		{
			throw new InvalidEntityException('Entity '. EntityHelper::toString($entity) . ' is not asociated with this entity.');
		}
		$this->parent->markAsChanged($this->parentParam);
		try {
			$entity->$param = NULL;
			$this->edit[spl_object_hash($entity)] = $entity;
		} catch (Exception $e) {
			$this->del[spl_object_hash($entity)] = $entity;
			// todo wtf chovani, kdyz nemuze existovat bez param tak se vymaze
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
			if (isset($this->del[spl_object_hash($entity)]))
			{
				return false;
			}
			$param = $this->param;
			return isset($entity->$param) AND $entity->$param === $this->parent;
		}
		return false;
	}

	/** @return IEntityCollection */
	final protected function getCollection()
	{
		if ($this->get === NULL)
		{
			if ($repository = $this->getChildRepository(false))
			{
				$method = 'findBy' . $this->param;
				$all = method_exists($repository, $method) ? $repository->$method($this->parent) : $repository->mapper->$method($this->parent);
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
					if (isset($entity->{$this->param}) AND $entity->{$this->param} === $this->parent)
					{
						// zkontroluje data nad uz vytvorenejma entitama, protoze ty entity v edit muzou mit parent = NULL
						$array[spl_object_hash($entity)] = $entity;
					}
				}
				foreach ($this->add as $hash => $entity)
				{
					if (isset($entity->{$this->param}) AND $entity->{$this->param} === $this->parent)
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

	/** @return void */
	final public function persist()
	{
		$repository = $this->getChildRepository();

		foreach ($this->del as $entity)
		{
			$repository->remove($entity);
		}


		if ($this->get)
		{
			foreach ($this->get as $entity)
			{
				$repository->persist($entity);
			}
		}
		else
		{
			foreach ($this->edit as $entity)
			{
				$repository->persist($entity);
			}
			$order = 0; // todo
			foreach ($this->add as $entity)
			{
				if ($entity->hasParam('order')) $entity->order = ++$order; // todo
				$repository->persist($entity);
			}
		}

		$this->del = $this->edit = $this->add = array();
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

	/**
	 * Vytvori / nacte / vrati entitu.
	 * if invasive: Smaze ji z poli edit, del a add. Vyprazdni get.
	 * @param IEntity|scalar|array
	 * @param bool
	 * @return IEntity|NULL null only if not invasive
	 */
	final protected function createEntity($entity, $invasive = true)
	{
		$entity = parent::createEntity($entity, $invasive);
		if ($invasive)
		{
			$hash = spl_object_hash($entity);
			unset($this->add[$hash], $this->edit[$hash], $this->del[$hash]);
			$this->get = NULL;
		}
		return $entity;
	}

}

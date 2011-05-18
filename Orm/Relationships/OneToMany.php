<?php

namespace Orm;

use UnexpectedValueException;
use Exception;

require_once dirname(__FILE__) . '/IRelationship.php';
require_once dirname(__FILE__) . '/BaseToMany.php';

class OneToMany extends BaseToMany implements IRelationship
{
	/** @var Entity */
	private $parent;

	/** @var string */
	private $param;

	/** @var IEntityCollection @see self::get() */
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
	 */
	public function __construct(IEntity $parent, $repository, $param)
	{
		$this->parent = $parent;
		$this->param = $param;
		parent::__construct($repository);
	}

	/**
	 * @param IEntity|scalar|array
	 * @return IEntity|NULL
	 */
	final public function add($entity)
	{
		$param = $this->param;
		$entity = $this->createEntity($entity);
		if ($this->ignore($entity)) return NULL;
		if (isset($entity->$param) AND $entity->$param !== NULL AND $entity->$param !== $this->parent)
		{
			$id = isset($entity->id) ? '#' . $entity->id : NULL;
			throw new UnexpectedValueException('Entity '. get_class($entity) . "$id is already asociated with another entity.");
		}
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
			$id = isset($entity->id) ? '#' . $entity->id : NULL;
			throw new UnexpectedValueException('Entity '. get_class($entity) . "$id is not asociated with this entity.");
		}
		try {
			$entity->$param = NULL;
			$this->edit[spl_object_hash($entity)] = $entity;
		} catch (Exception $e) {
			$this->del[spl_object_hash($entity)] = $entity;
			// todo wtf chovani, kdyz nemuze existovat bez param tak se vymaze
		}
		return $entity;
	}

	/** @return IEntityCollection */
	final public function get()
	{
		if (!isset($this->get))
		{
			if ($this->parent->getModel(false))
			{
				$repository = $this->getChildRepository();
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

	public function persist()
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

	/** @return RepositoryContainer */
	public function getModel()
	{
		return $this->parent->getModel();
	}

	/**
	 * Vytvori / nacte / vrati entitu.
	 * Smaze ji z poli edit, del a add. Vyprazdni get.
	 * @param IEntity|scalar|array
	 * @return IEntity
	 */
	final protected function createEntity($entity)
	{
		$entity = parent::createEntity($entity);
		$hash = spl_object_hash($entity);
		unset($this->add[$hash], $this->edit[$hash], $this->del[$hash]);
		$this->get = NULL;
		return $entity;
	}

}

require_once dirname(__FILE__) . '/bc1m.php';

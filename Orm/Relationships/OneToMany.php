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
		$param = $this->getMetaData()->getChildParam();
		$parent = $this->getParent();
		if (isset($entity->$param) AND $entity->$param !== NULL AND $entity->$param !== $parent)
		{
			throw new InvalidEntityException('Entity '. EntityHelper::toString($entity) . ' is already asociated with another entity.');
		}
		$parent->markAsChanged($this->getMetaData()->getParentParam());
		$entity->$param = $parent;
		$hash = spl_object_hash($entity);
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
		if (!isset($entity->$param) OR $entity->$param !== $parent)
		{
			throw new InvalidEntityException('Entity '. EntityHelper::toString($entity) . ' is not asociated with this entity.');
		}
		$parent->markAsChanged($this->getMetaData()->getParentParam());
		$hash = spl_object_hash($entity);
		unset($this->add[$hash], $this->edit[$hash], $this->del[$hash]);
		try {
			$entity->$param = NULL;
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

	/** @return IEntityCollection */
	final protected function getCollection()
	{
		if ($this->get === NULL)
		{
			$param = $this->getMetaData()->getChildParam();
			$parent = $this->getParent();
			if ($repository = $this->getChildRepository(false))
			{
				$method = 'findBy' . $param;
				$all = method_exists($repository, $method) ? $repository->$method($parent) : $repository->mapper->$method($parent);
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

}

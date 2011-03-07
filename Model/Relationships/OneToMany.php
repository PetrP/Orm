<?php

require_once dirname(__FILE__) . '/IRelationship.php';

class OneToMany extends Object implements IteratorAggregate, Countable, IRelationship
{
	/** @var Entity */
	private $parent;

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

	/** @var string cache @see self::getSecondParamName() */
	private $param;

	/** @param IEntity $parent */
	public function __construct(IEntity $parent)
	{
		$entityName = $this->getFirstEntityName();
		if (!($parent instanceof $entityName))
		{
			throw new UnexpectedValueException(get_class($this) . " expected '$entityName' as parent, " . get_class($parent) . ' given.');
		}
		$this->parent = $parent;
		$this->param = $this->getSecondParamName();
	}

	/**
	 * @param IEntity|scalar|array
	 * @return IEntity
	 */
	final public function add($entity)
	{
		$param = $this->param;
		$entity = $this->createEntity($entity);
		if (isset($entity->$param) AND $entity->$param !== NULL) throw new Exception(); // todo
		$entity->$param = $this->parent;
		$this->add[spl_object_hash($entity)] = $entity;
		return $entity;
	}

	/**
	 * @param array of IEntity|scalar|array
	 * @return IRelationship $this
	 */
	final public function set(array $data)
	{
		foreach ($this->get() as $entity)
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
	 * @param IEntity|scalar|array
	 * @return IEntity
	 */
	final public function remove($entity)
	{
		$param = $this->param;
		$entity = $this->createEntity($entity);
		if (!isset($entity->$param) AND $entity->$param !== $this->param) throw new Exception(); // todo
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
			$repository = $this->getSecondRepository();
			$method = 'findBy' . $this->param;
			$all = method_exists($repository, $method) ? $repository->$method($this->parent) : $repository->mapper->$method($this->parent);
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
				if ($this->add) foreach ($this->add as $hash => $entity)
				{
					if (isset($entity->{$this->param}) AND $entity->{$this->param} === $this->parent)
					{
						unset($array[$hash]);
						$array[$hash] = $entity;
					}
				}
				if ($this->del)
				{
					foreach ($this->del as $hash => $entity)
					{
						unset($array[$hash]);
					}
				}
				$all = new ArrayDataSource($array);
			}
			$this->get = $all;
		}
		return $this->get;
	}

	public function persist()
	{
		$repository = $this->getSecondRepository();
		foreach ($this->del as $entity)
		{
			$repository->remove($entity);
		}

		foreach ($this->edit as $entity)
		{
			$repository->persist($entity);
		}

		$order = 0;
		foreach ($this->add as $entity)
		{
			if ($entity->hasParam('order')) $entity->order = ++$order; // todo
			$repository->persist($entity);
		}

		$this->del = $this->edit = $this->add = array();
	}

	/** @return int */
	public function count()
	{
		return $this->get()->count();
	}

	/** @return Traversable */
	public function getIterator()
	{
		return $this->get()->getIterator();
	}

	/** @return Model */
	public function getModel()
	{
		return $this->parent->getModel();
	}

	/**
	 * Nazev entity s kterou na kterou se pripojuje.
	 * @return string
	 */
	protected function getFirstEntityName()
	{
		return substr(get_class($this), 0, strpos(get_class($this), 'To'));
	}

	/**
	 * Nazev parametru na pripojenych entitach.
	 * @return string
	 */
	protected function getSecondParamName()
	{
		$param =  $this->getFirstEntityName();;
		if ($param{0} != '_') $param{0} = $param{0} | "\x20";
		return $param;
	}

	/**
	 * Repository
	 * @return Repository
	 */
	protected function getSecondRepository()
	{
		return $this->getModel()->getRepository(substr(get_class($this), strpos(get_class($this), 'To') + 2));
	}

	/**
	 * Vytvori / nacte / vrati entitu.
	 * Smaze ji z poli edit, del a add. Vyprazdni get.
	 * @param IEntity|scalar|array
	 * @return IEntity
	 */
	private function createEntity($entity)
	{
		$repository = $this->getSecondRepository();
		if ($entity instanceof IEntity)
		{
			// $entity
		}
		else if (is_scalar($entity))
		{
			$entity = $repository->getById($entity);
			if (!$entity) throw new Exception(); // todo
		}
		else if (is_array($entity) OR $entity instanceof Traversable)
		{
			$array = $entity instanceof Traversable ? iterator_to_array($entity) : $entity;
			$entity = NULL;
			if (isset($array['id']))
			{
				$entity = $repository->getById($array['id']);
			}
			if (!$entity)
			{
				$entityName = $repository->getEntityClassName($array);
				$entity = new $entityName; // todo construct pak nesmy mit povine parametry
			}
			$entity->setValues($array);
		}
		$hash = spl_object_hash($entity);
		unset($this->add[$hash], $this->edit[$hash], $this->del[$hash]);
		$this->get = NULL;
		return $entity;
	}

	/** @deprecated */
	final protected function compare(& $all, $row) {throw new DeprecatedException();}
	/** @deprecated */
	final protected function row($row) {throw new DeprecatedException();}
	/** @deprecated */
	final protected function prepareAllForSet() {throw new DeprecatedException();}

}

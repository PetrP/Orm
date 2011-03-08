<?php

require_once dirname(__FILE__) . '/IRelationship.php';

require_once dirname(__FILE__) . '/DibiManyToManyMapper.php';

// todo moznost mit parametry/ data
// ->add($entity, array('time' => time()))
// todo rict entity ze se zmenila
class ManyToMany extends Object implements IRelationship
{

	protected $parentRepository;
	protected $childRepository;

	private $parent;

	private $get;
	private $add = array();
	private $del = array();

	private $mapper;

	public function __construct(IEntity $entity, $name = NULL)
	{
		$this->parent = $entity;
		$firstRepository = $this->getFirstRepository();
		$secondRepository = $this->getSecondRepository();

		if ($firstRepository->isEntity($entity))
		{
			$this->parentRepository = $firstRepository;
			$this->childRepository = $secondRepository;
			$parentIsFirst = true;
		}
		else if ($this->getSecondRepository()->isEntity($entity))
		{
			$this->parentRepository = $secondRepository;
			$this->childRepository = $firstRepository;
			$parentIsFirst = false;
		}
		else
		{
			throw new UnexpectedValueException();
		}
		$this->getMapper()->setParams($parentIsFirst, $firstRepository, $secondRepository);
	}

	/**
	 * @param IEntity|int|array
	 * @return IEntity
	 */
	final public function add($entity)
	{
		$entity = $this->createEntity($entity);
		// $entity->manytomany->add($this->parent); // todo kdyz existuje?
		$this->add[spl_object_hash($entity)] = $entity;
		return $entity;
	}

	/**
	 * @param array of IEntity|scalar|array
	 * @return IRelationship $this
	 */
	final public function set(array $data)
	{
		foreach ($this->get() as $entity) // todo teoreticky vytahuju zbytecne, stacilo by oznacit ze smazat vsechny
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
		$entity = $this->createEntity($entity);
		// $entity->manytomany->remove($this->parent); // todo kdyz existuje?
		$this->del[spl_object_hash($entity)] = $entity;
		return $entity;
	}

	/** @return IEntityCollection */
	final public function get()
	{
		if (!isset($this->get))
		{
			$ids = $this->getMapper()->load($this->parent);
			$all = $ids ? $this->childRepository->mapper->findById($ids) : new ArrayDataSource(array());
			if ($this->add OR $this->del)
			{
				$array = array();
				foreach ($all as $entity)
				{
					$array[spl_object_hash($entity)] = $entity;
				}
				foreach ($this->add as $hash => $entity)
				{
					unset($array[$hash]);
					$array[$hash] = $entity;
				}
				foreach ($this->del as $hash => $entity)
				{
					unset($array[$hash]);
				}
				$all = new ArrayDataSource($array);
			}
			$this->get = $all;
		}
		return $this->get;
	}

	public function persist()
	{
		$repository = $this->childRepository;

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

		if ($add) $this->getMapper()->add($this->parent, $add);
		if ($del) $this->getMapper()->remove($this->parent, $del);

		$this->del = $this->add = array();
		if ($this->get instanceof ArrayDataSource) $this->get = NULL; // free memory
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

	public function getInjectedValue()
	{
	}

	public function setInjectedValue($value)
	{
		if ($value !== NULL) $this->set($value);
	}

	public static function create($className, IEntity $entity, $value = NULL, $name = NULL)
	{
		return new $className($entity, $name);
		return $r;
	}

	/** @return Repository */
	protected function getFirstRepository()
	{
		return $this->getModel()->getRepository(substr(get_class($this), 0, strpos(get_class($this), 'To')));
	}

	/** @return Repository */
	protected function getSecondRepository()
	{
		return $this->getModel()->getRepository(substr(get_class($this), strpos(get_class($this), 'To') + 2));
	}

	protected function createMapper()
	{
		$mapper1 = $this->parentRepository->getMapper();
		$mapper2 = $this->childRepository->getMapper();
		// and mam protejsi property (protoze pole je potreba udrzovat na obou stranach)
		if ($mapper1 instanceof ArrayMapper AND $mapper2 instanceof ArrayMapper)
		{
			return new ArrayManyToManyMapper;
		}
		else if ($mapper1 instanceof DibiMapper OR $mapper2 instanceof DibiMapper)
		{
			return new DibiManyToManyMapper;
		}
	}

	protected function getMapper()
	{
		if (!isset($this->mapper))
		{
			$mapper = $this->createMapper();
			if (!($mapper instanceof IManyToManyMapper)) throw new Exception(); // todo
			$this->mapper = $mapper;
		}
		return $this->mapper;
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
				$entityName = $repository->getEntityClassName($array);
				$entity = new $entityName; // todo construct pak nesmy mit povine parametry
			}
			$entity->setValues($array);
		}
		if (!($entity instanceof IEntity))
		{
			$entity = $repository->getById($entity);
			if (!$entity) throw new Exception(); // todo
		}
		if (!$repository->isEntity($entity)) throw new UnexpectedValueException();
		$hash = spl_object_hash($entity);
		unset($this->add[$hash], $this->del[$hash]);
		$this->get = NULL;
		return $entity;
	}

	final protected function getFirstParamName() {throw new DeprecatedException();}
	final protected function getSecondParamName() {throw new DeprecatedException();}
	final protected function getParentParamName() {throw new DeprecatedException();}
	final protected function getChildParamName() {throw new DeprecatedException();}
	final protected function ignore(IEntity $entity, array $data = NULL) {throw new DeprecatedException();}
}

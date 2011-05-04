<?php

require_once dirname(__FILE__) . '/IRelationship.php';

require_once dirname(__FILE__) . '/DibiManyToManyMapper.php';

require_once dirname(__FILE__) . '/ArrayManyToManyMapper.php';

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

	private $model;

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

		$mapper = $this->createMapper($firstRepository, $secondRepository);
		if (!($mapper instanceof IManyToManyMapper)) throw new Exception(); // todo
		$mapper->setParams($parentIsFirst, $firstRepository, $secondRepository);
		$this->mapper = $mapper;
	}

	/**
	 * @param IEntity|int|array
	 * @return IEntity|NULL
	 */
	final public function add($entity)
	{
		$entity = $this->createEntity($entity);
		if ($this->ignore($entity)) return NULL;
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
			$all = $ids ? $this->childRepository->mapper->findById($ids) : new ArrayCollection(array());
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
				$all = new ArrayCollection($array);
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
		if ($this->get instanceof ArrayCollection) $this->get = NULL; // free memory
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
		if (!isset($this->model))
		{
			$this->model = $this->parent->getModel();
		}
		return $this->model;
	}

	public function getInjectedValue()
	{
		$mapper = $this->getMapper();
		if ($mapper instanceof ArrayManyToManyMapper)
		{
			return $mapper->value;
		}
	}

	public function setInjectedValue($value)
	{
		if ($value !== NULL) $this->set($value);
	}

	public static function create($className, IEntity $entity, $value = NULL, $name = NULL)
	{
		$r = new $className($entity, $name);
		$mapper = $r->getMapper();
		if ($mapper instanceof ArrayManyToManyMapper)
		{
			$mapper->value = ValidationHelper::isValid(array('array'), $value) ? $value : array();
		}
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

	protected function createMapper(IRepository $firstRepository, IRepository $secondRepository)
	{
		return $firstRepository->getMapper()->createDefaultManyToManyMapper();
		// todo array jen kdyz mam na obou stranach arraymapper a mam protejsi property (protoze pole je potreba udrzovat na obou stranach)
	}

	protected function getMapper()
	{
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

	protected function ignore(IEntity $entity)
	{
		return false;
	}

	final protected function getFirstParamName() {throw new DeprecatedException();}
	final protected function getSecondParamName() {throw new DeprecatedException();}
	final protected function getParentParamName() {throw new DeprecatedException();}
	final protected function getChildParamName() {throw new DeprecatedException();}

}

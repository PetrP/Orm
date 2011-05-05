<?php

require_once dirname(__FILE__) . '/IRelationship.php';

require_once dirname(__FILE__) . '/BaseToMany.php';

require_once dirname(__FILE__) . '/DibiManyToManyMapper.php';

require_once dirname(__FILE__) . '/ArrayManyToManyMapper.php';

// todo rict parent entity ze se zmenila
class ManyToMany extends BaseToMany implements IRelationship
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
	 * Odebrane z kolekce.
	 * @var array of IEntity
	 * @see self::remove()
	 */
	private $del = array();

	/**
	 * @see self::getMapper()
	 * @see self::createMapper()
	 * @var IManyToManyMapper
	 */
	private $mapper;

	/**
	 * @see ArrayManyToManyMapper
	 * @see self::getMapper()
	 * @var mixed
	 */
	private $initialValue;

	/**
	 * @param IEntity
	 * @param IRepository|string repositoryName for lazy load
	 * @param string|NULL m:1 param on child entity
	 * @param mixed
	 */
	public function __construct(IEntity $parent, $repository, $param = NULL, $value = NULL)
	{
		$this->parent = $parent;
		$this->param = $param;
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
	final public function get()
	{
		if (!isset($this->get))
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

	/** @see IManyToManyMapper */
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

	/** @return Model */
	public function getModel()
	{
		return $this->parent->getModel();
	}

	/** @return mixed */
	public function getInjectedValue()
	{
		$mapper = $this->getMapper();
		if ($mapper instanceof ArrayManyToManyMapper)
		{
			return $mapper->value;
		}
	}

	/**
	 * @see self::getMapper()
	 * @param IRepository
	 * @param IRepository
	 * @return IManyToManyMapper
	 */
	protected function createMapper(IRepository $firstRepository, IRepository $secondRepository)
	{
		return $firstRepository->getMapper()->createDefaultManyToManyMapper();
		// todo array jen kdyz mam na obou stranach arraymapper a mam protejsi property (protoze pole je potreba udrzovat na obou stranach)
	}

	/**
	 * @see self::createMapper()
	 * @return IManyToManyMapper
	 */
	protected function getMapper()
	{
		if (!isset($this->mapper))
		{
			if ($this->parent->getModel(false))
			{
				$repository = $this->getChildRepository();
				$mapper = $this->createMapper($this->parent->getGeneratingRepository(), $repository);
				if (!($mapper instanceof IManyToManyMapper))
				{
					throw new InvalidStateException(get_class($this) . "::createMapper() must return IManyToManyMapper, '" . (is_object($mapper) ? get_class($mapper) : gettype($mapper)) . "' given");
				}
				$mapper->setParams(true /* todo */, $this->parent->getGeneratingRepository(), $repository); // todo

				if ($mapper instanceof ArrayManyToManyMapper)
				{
					$mapper->value = ValidationHelper::isValid(array('array'), $this->initialValue) ? $this->initialValue : array();
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

require_once dirname(__FILE__) . '/bcmm.php';

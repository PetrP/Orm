<?php

require_once dirname(__FILE__) . '/IRepository.php';

abstract class Repository extends Object implements IRepository
{
	private $mapper;

	private $repositoryName;

	/** @var SqlConventional */
	protected $conventional;

	private $entities = array();

	private $performanceHelper;

	public function getById($id)
	{
		$this->performanceHelper->access($id);
		if ($id instanceof Entity)
		{
			$id = $id->id;
		}
		if (isset($this->entities[$id]))
		{
			return $this->entities[$id];
		}
		$ids = $this->performanceHelper->get();
		if ($ids) $this->mapper->findById($ids)->fetchAll();
		if (isset($this->entities[$id]))
		{
			return $this->entities[$id];
		}

		return $this->getMapper()->getById($id);
	}

	public function lazyLoad(Entity $entity, $param)
	{
		return array();
	}

	public function __construct($repositoryName)
	{
		$this->repositoryName = $repositoryName;
		$this->conventional = $this->getMapper()->getConventional(); // speedup
		$this->performanceHelper = new PerformanceHelper($this);
	}

	final public function getMapper()
	{
		if (!isset($this->mapper))
		{
			$mapper = $this->createMapper();
			if (!($mapper instanceof Mapper))
			{
				throw new InvalidStateException();
			}
			$this->mapper = $mapper;
		}
		return $this->mapper;
	}

	protected function createMapper()
	{
		$class = $this->getRepositoryName() . 'Mapper';
		$class{0} = $class{0} & "\xDF"; // ucfirst
		if (class_exists($class))
		{
			return new $class($this);
		}
		return new DibiMapper($this);
	}

	final public function getEntityName(array $data = NULL) // todo rename? getEntityClassName?
	{
		throw new DeprecatedException();

		//return unserialize("O:".strlen($n).":\"$n\":1:{s:14:\"\0Entity\0params\";".serialize($data->d)."}");
		//return call_user_func(array($entityName, 'create'), $entityName, (array) $data);
	}

	public function getEntityClassName(array $data = NULL)
	{
		return rtrim($this->getRepositoryName(), 's');
	}

	private $allowedEntities;
	final private function checkEntityName($entityName)
	{
		if (!isset($this->allowedEntities))
		{
			$this->allowedEntities = array_fill_keys(array_map('strtolower',(array) $this->getEntityClassName()), true);
		}
		// todo strtolower mozna bude moc pomale
		if (!isset($this->allowedEntities[strtolower($entityName)]))
		{
			throw new UnexpectedValueException();
		}
	}

	final public function isEntity(Entity $entity) // todo rename
	{
		try {
			$this->checkEntityName(get_class($entity));
		} catch (Exception $e) {
			return false;
		}
		return true;
	}

	final public function createEntity($data)
	{
		if (!isset($this->entities[$data['id']]))
		{
			$data = (array) $this->conventional->formatStorageToEntity($data);
			$entityName = $this->getEntityClassName($data);
			$this->checkEntityName($entityName);
			$this->entities[$data['id']] = Entity::create($entityName, $data, $this);
		}
		return $this->entities[$data['id']];
	}

	public function __call($name, $args)
	{
		return call_user_func_array(array($this->getMapper(), $name), $args);
	}

	final public function getRepositoryName()
	{
		return $this->repositoryName;
	}

	public function persist(Entity $entity, $beAtomic = true)
	{
		$this->checkEntityName(get_class($entity));
		if (isset($entity->id) AND !$entity->isChanged())
		{
			return $entity->id;
		}
		return $this->getMapper()->persist($entity, $beAtomic);
	}

	public function delete($entity, $beAtomic = true) // todo prejmenovat na remove?
	{
		if ($entity instanceof Entity) $this->checkEntityName(get_class($entity));
		return $this->getMapper()->delete($entity, $beAtomic);
	}

}

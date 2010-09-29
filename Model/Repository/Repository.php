<?php

require_once dirname(__FILE__) . '/IRepository.php';

abstract class Repository extends Object implements IRepository
{
	/** @var Model */
	private $model;

	/** @var DibiMapper */
	private $mapper;

	private $repositoryName;

	/** @var SqlConventional */
	protected $conventional;

	private $entities = array();

	/** @var PerformanceHelper */
	private $performanceHelper;

	public function getById($id)
	{
		if ($id instanceof IEntity)
		{
			$id = $id->id;
		}
		else if ($id === NULL)
		{
			return NULL;
		}
		else if (!is_scalar($id))
		{
			throw new UnexpectedValueException();
		}
		$this->performanceHelper->access($id);
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

	public function lazyLoad(IEntity $entity, $param)
	{
		return array();
	}

	public function __construct($repositoryName, Model $model)
	{
		$this->model = $model;
		$this->repositoryName = $repositoryName;
		$this->conventional = $this->getMapper()->getConventional(); // speedup
		$this->performanceHelper = new PerformanceHelper($this);
	}

	final public function getMapper()
	{
		if (!isset($this->mapper))
		{
			$mapper = $this->createMapper();
			if (!($mapper instanceof IMapper))
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

	final public function getModel()
	{
		return $this->model;
	}

	final public function getEntityName(array $data = NULL)
	{
		throw new DeprecatedException();
	}

	/**
	 * @param array|NULL
	 * @return string|array
	 */
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

	final public function isEntity(IEntity $entity) // todo rename
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
			$this->entities[$data['id']] = $entity = Entity::___create($entityName, $data, $this);
			Entity::___event($entity, 'load', $this);
		}
		return $this->entities[$data['id']];
	}

	final public function getRepositoryName()
	{
		return $this->repositoryName;
	}

	public function persist(IEntity $entity)
	{
		$this->checkEntityName(get_class($entity));
		$hasId = isset($entity->id);
		if ($hasId AND !$entity->isChanged())
		{
			return $entity->id;
		}
		Entity::___event($entity, 'beforePersist', $this);
		Entity::___event($entity, $hasId ? 'beforeUpdate' : 'beforeInsert', $this);

		$relationshipValues = array();
		$fk = Entity::___getFk(get_class($entity));
		foreach ($entity->toArray() as $key => $value)
		{
			if (isset($fk[$key]) AND $value instanceof IEntity)
			{
				$this->getModel()->getRepository($fk[$key])->persist($value, false);
			}
			else if ($value instanceof IRelationship)
			{
				$relationshipValues[] = $value;;
			}
		}

		if ($id = $this->getMapper()->persist($entity))
		{
			Entity::___event($entity, 'persist', $this, $id);
			$this->entities[$entity->id] = $entity;

			foreach ($relationshipValues as $relationship)
			{
				$relationship->persist();
			}

			Entity::___event($entity, $hasId ? 'afterUpdate' : 'afterInsert', $this);
			Entity::___event($entity, 'afterPersist', $this);
			return $id;
		}
		throw new Exception(); // todo
	}

	public function delete($entity) // todo prejmenovat na remove?
	{
		$entity = $entity instanceof IEntity ? $entity : $this->getById($entity);
		$this->checkEntityName(get_class($entity));

		Entity::___event($entity, 'beforeDelete', $this);
		if (isset($entity->id))
		{
			if ($this->getMapper()->delete($entity))
			{
				unset($this->entities[$entity->id]);
			}
			else
			{
				throw new Exception(); // todo
			}
		}
		Entity::___event($entity, 'afterDelete', $this);
		return true;
	}

	public function flush($onlyThis = false)
	{
		if ($onlyThis) return $this->getMapper()->flush();
		return $this->getModel()->flush();
	}

	public function clean($onlyThis = false)
	{
		if ($onlyThis) return $this->getMapper()->rollback();
		return $this->getModel()->clean();
	}

}

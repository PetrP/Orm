<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * Identity map over repository.
 * And also check and hydrate entity (for optimization reasons).
 * @todo prejmenovat vetsinu metod
 * @author Petr Procházka
 * @package Orm
 * @subpackage Repository\Helpers
 */
class IdentityMap extends Object
{

	/** @var IRepository */
	private $repository;

	/** @var Events */
	private $events;

	/** @var PerformanceHelper|NULL */
	private $performanceHelper;

	/** @var array */
	private $entities = array();

	/** @var array */
	private $newEntities = array();

	/** @var array cache {@see self::checkEntityClassName()} */
	private $allowedEntities;

	/** @var SqlConventional */
	private $conventional;

	/** @var string {@see IDatabaseConventional::getPrimaryKey()} */
	private $primaryKey;

	/**
	 * @param IRepository
	 * @param PerformanceHelper|NULL
	 */
	public function __construct(IRepository $repository, PerformanceHelper $performanceHelper = NULL)
	{
		$this->repository = $repository;
		$this->events = $repository->getEvents();
		$this->performanceHelper = $performanceHelper;

		$allowedEntities = array();
		foreach ((array) $this->repository->getEntityClassName() as $en)
		{
			if (!class_exists($en))
			{
				throw new InvalidEntityException(get_class($this->repository) . ": entity '$en' does not exists; see property Orm\\Repository::\$entityClassName or method Orm\\IRepository::getEntityClassName()");
			}
			$allowedEntities[strtolower($en)] = true;
		}
		$this->allowedEntities = $allowedEntities;
	}

	/**
	 * @param scalar
	 * @return IEntity|false|NULL
	 * 	false = urcite neexistuje
	 * 	NULL = zatim neni v identity map
	 * @see IRepository::getById()
	 */
	public function getById($id)
	{
		if ($this->performanceHelper)
		{
			// nactu vsechny ktere budu pravdepodobne potrebovat (jen jednou)
			if ($ids = $this->performanceHelper->get())
			{
				$this->repository->getMapper()->findById($ids)->fetchAll();
				$this->entities += array_fill_keys($ids, false);
				return $this->getById($id);
			}
			$this->performanceHelper->access($id);
		}

		if (isset($this->entities[$id]))
		{
			return $this->entities[$id];
		}
		return NULL;
	}

	/**
	 * Add to identity map by his id.
	 * @param scalar
	 * @param IEntity
	 * @return void
	 * @see IRepository::persist()
	 */
	public function add($id, IEntity $entity)
	{
		$this->entities[$id] = $entity;
	}

	/**
	 * Remove from identity map by his id.
	 * @param scalar
	 * @return void
	 * @see IRepository::remove()
	 */
	public function remove($id)
	{
		$this->entities[$id] = false;
	}

	/**
	 * Attach as new not persisted entity.
	 * @param IEntity
	 * @return void
	 * @see IRepository::attach()
	 */
	public function attach(IEntity $entity)
	{
		$this->newEntities[spl_object_hash($entity)] = $entity;
	}

	/**
	 * Detach as new not persisted entity.
	 * @param IEntity
	 * @return void
	 * @see IRepository::remove()
	 */
	public function detach(IEntity $entity)
	{
		unset($this->newEntities[spl_object_hash($entity)]);
	}

	/**
	 * Vytvori entity, nebo vrati tuto existujici.
	 * @param array
	 * @return IEntity
	 * @see IRepository::hydrateEntity()
	 */
	public function create($data)
	{
		if ($this->conventional === NULL)
		{
			$this->conventional = $this->repository->getMapper()->getConventional(); // speedup
			$this->primaryKey = $this->conventional->getPrimaryKey();
		}
		if (!isset($data[$this->primaryKey]))
		{
			throw new BadReturnException("Data, that is returned from storage, doesn't contain id.");
		}
		$id = $data[$this->primaryKey];
		if (!isset($this->entities[$id]) OR !$this->entities[$id])
		{
			$data = (array) $this->conventional->formatStorageToEntity($data);
			$entityName = $this->repository->getEntityClassName($data);
			$this->checkEntityClassName($entityName);
			$entity = unserialize("O:".strlen($entityName).":\"$entityName\":0:{}");
			if (!($entity instanceof IEntity)) throw new InvalidEntityException('Unserialize error');
			$args = array('data' => $data);
			$this->events->fireEvent(Events::HYDRATE_BEFORE, $entity, $args);
			$id = $entity->id;
			$this->entities[$id] = $entity;
			$this->events->fireEvent(Events::HYDRATE_AFTER, $entity, $args);
		}
		return $this->entities[$id];
	}

	/**
	 * Check if it is correct entity.
	 * @param IEntity
	 * @param bool
	 * @return bool
	 * @throws InvalidEntityException
	 * @see IRepository::getEntityClassName()
	 */
	public function check(IEntity $entity, $throw = true)
	{
		if (!$this->checkEntityClassName(get_class($entity), $throw))
		{
			return false;
		}
		if (!$this->checkEntityRepository($entity, $throw))
		{
			return false;
		}
		return true;
	}

	/** @return array of id => IEntity all entity with id handled by identity map */
	public function getAll()
	{
		return $this->entities;
	}

	/** @return array of IEntity all entity without id handled by identity map */
	public function getAllNew()
	{
		return array_values($this->newEntities);
	}

	/**
	 * Check if class name of entity is valid.
	 * @param string
	 * @param bool
	 * @return bool
	 * @throws InvalidEntityException
	 * @see IRepository::getEntityClassName()
	 * @todo strtolower mozna bude moc pomale
	 */
	private function checkEntityClassName($entityName, $throw = true)
	{
		if (!isset($this->allowedEntities[strtolower($entityName)]))
		{
			if ($throw)
			{
				$tmp = (array) $this->repository->getEntityClassName();
				$tmpLast = array_pop($tmp);
				$tmp = $tmp ? "'" . implode("', '", $tmp) . "' or '$tmpLast'" : "'$tmpLast'";
				throw new InvalidEntityException(get_class($this->repository) . " can't work with entity '$entityName', only with $tmp");
			}
			return false;
		}
		return true;
	}

	/**
	 * Check if entity is not attached to another repository.
	 * @param IEntity
	 * @param bool
	 * @return bool
	 * @throws InvalidEntityException
	 */
	private function checkEntityRepository(IEntity $entity, $throw = true)
	{
		$r = $entity->getRepository(false);
		if ($r AND $r !== $this->repository)
		{
			if ($throw)
			{
				throw new InvalidEntityException(EntityHelper::toString($entity) . ' is attached to another repository.');
			}
			return false;
		}
		return true;
	}
}

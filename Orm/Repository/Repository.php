<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use Exception;

/**
 * Handles entities.
 * Independently of the specific storage.
 * Saving, deleting, loading entities.
 *
 * For each entity type (or group of related entities) you must create own repository.
 *
 * Convention is named repository plural form and entity singular form.
 *
 * <code>
 * class ArticlesRepository extends Repository
 * </code>
 *
 * Repository must be obtained via IRepositoryContainer {@see RepositoryContainer}
 * <code>
 * $model; // instanceof RepositoryContainer
 * $model->articles; // instanceof ArticlesRepository
 * </code>
 *
 * Repository is independently of the specific storage.
 * About storage is cares Mapper {@see IMapper} {@see DibiMapper}
 *
 * Naming convention methods for retrieving data:
 * `getBy<...>()` for one entity {@see IEntity}
 * `findBy<...>()` for collection of entities {@see IEntityCollection}
 * `findAll()` all entities
 *
 * You can automatically call methods in mapper like `$mapper->findByAuthorAndTag($author, $tag)` etc.
 * But in repository is needed to create all methods:
 * <code>
 * public function findByAuthor($author)
 * {
 * 	return $this->mapper->findByAuthor($author);
 * }
 * public function getByName($name)
 * {
 * 	return $this->mapper->getByName($name);
 * }
 * </code>
 *
 * Defaults repository works with entity named by repository name in singular form `ArticlesRepository > Article` {@see self::getEntityClassName()}.
 *
 * Defaults tries find mapper by repository name `ArticlesRepository > ArticlesMapper`
 * It can be changed by annotation `@mappper`.
 *
 * @see self::getById() Get one entity by primary key.
 * @see self::persist() Saving.
 * @see self::remove() Deleting.
 * @see self::flush() Make changes in storage.
 * @see self::clean() Clear changes in storage.
 *
 * @property-read IMapper $mapper
 * @property-read IRepositoryContainer $model
 * @property-read Events $events
 * @author Petr Procházka
 * @package Orm
 * @subpackage Repository
 */
abstract class Repository extends Object implements IRepository
{

	/** @var IRepositoryContainer */
	private $model;

	/** @var DibiMapper */
	private $mapper;

	/** @var Events */
	private $events;

	/** @var SqlConventional */
	private $conventional;

	/** @var string @see IDatabaseConventional::getPrimaryKey() */
	private $primaryKey;

	/** @var array @todo refaktorovat */
	private $entities = array();

	/** @var PerformanceHelper|NULL */
	private $performanceHelper;

	/** @var array cache {@see self::checkAttachableEntity() */
	private $allowedEntities;

	/** @var MapperAutoCaller {@see self::__call()} */
	private $mapperAutoCaller;

	/**
	 * @var string
	 * @see self::getEntityClassName()
	 */
	protected $entityClassName;

	/**
	 * @param IRepositoryContainer
	 */
	public function __construct(IRepositoryContainer $model)
	{
		$this->model = $model;
		$this->events = new Events($this);
		$ph = $this->createPerformanceHelper();
		if ($ph !== NULL AND !($ph instanceof PerformanceHelper))
		{
			throw new BadReturnException(array($this, 'createPerformanceHelper', 'Orm\PerformanceHelper or NULL', $ph));
		}
		$this->performanceHelper = $ph;
	}

	/**
	 * @param scalar
	 * @return IEntity|NULL
	 */
	final public function getById($id)
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
			throw new InvalidArgumentException(array($this, 'getById() $id', 'scalar', $id));
		}
		if ($this->performanceHelper)
		{
			$this->performanceHelper->access($id);
		}
		if (isset($this->entities[$id]))
		{
			if ($this->entities[$id]) return $this->entities[$id];
			return NULL;
		}

		// nactu vsechny ktere budu pravdepodobne potrebovat
		if ($this->performanceHelper AND $ids = $this->performanceHelper->get())
		{
			$this->getMapper()->findById($ids)->fetchAll();
			foreach ($ids as $tmp)
			{
				if (!isset($this->entities[$tmp])) $this->entities[$tmp] = false;
			}
			if (isset($this->entities[$id]))
			{
				if ($this->entities[$id]) return $this->entities[$id];
				return NULL;
			}
		}

		$entity = $this->getMapper()->getById($id);
		if (!$entity)
		{
			$this->entities[$id] = false;
		}
		return $entity;
	}

	/**
	 * Zapoji entity do do repository.
	 *
	 * Vola udalosti:
	 * @see Events::ATTACH
	 * @see Entity::onAttach()
	 *
	 * @param IEntity
	 * @return IEntity
	 */
	public function attach(IEntity $entity)
	{
		$this->checkAttachableEntity(get_class($entity), $entity);
		if (!$entity->getRepository(false))
		{
			$this->events->fireEvent(Events::ATTACH, $entity);
		}
		return $entity;
	}

	/**
	 * Ulozit entitu {@see IMapper::persist()} a zapoji ji do repository {@see self::attach()}
	 * Jen kdyz se zmenila {@see Entity::isChanged()}
	 *
	 * Ulozi take vsechny relationship, tedy entity ktere tato entity obsahuje v ruznych vazbach.
	 *
	 * Vola udalosti:
	 * @see Events::PERSIST_BEFORE
	 * @see Entity::onBeforePersist()
	 * @see Events::PERSIST_BEFORE_UPDATE OR Events::PERSIST_BEFORE_INSERT
	 * @see Entity::onBeforeUpdate() OR Entity::onBeforeInsert()
	 * @see Events::PERSIST
	 * @see Entity::onPersist()
	 * @see Events::PERSIST_AFTER_UPDATE OR Events::PERSIST_AFTER_INSERT
	 * @see Entity::onAfterUpdate() OR Entity::onAfterInsert()
	 * @see Events::PERSIST_AFTER
	 * @see Entity::onAfterPersist()
	 *
	 * @param IEntity
	 * @return IEntity
	 */
	final public function persist(IEntity $entity)
	{
		$this->attach($entity);
		$hasId = isset($entity->id);
		if ($hasId)
		{
			$hasId = $entity->id;
			if (!$entity->isChanged())
			{
				return $entity;
			}
		}
		$recursionRelationship = array();
		static $recursionRelationshipCallback;
		if (!$recursionRelationshipCallback) $recursionRelationshipCallback = function (IEntity $entity, array & $recursionRelationship, $persist = false) {
			foreach ($recursionRelationship as $k => $tmp)
			{
				list($repository, $key, $value) = $tmp;
				if ($persist)
				{
					$repository->persist($value);
				}
				if ($key !== NULL)
				{
					$entity->{$key} = $value;
				}
				unset($recursionRelationship[$k]);
			}
		};
		try {
			$hash = spl_object_hash($entity);
			static $recurcion = array();
			if (isset($recurcion[$hash]) AND $recurcion[$hash] > 1)
			{
				throw new RecursiveException("There is an infinite recursion during persist in " . EntityHelper::toString($entity), $recurcion[$hash]);
			}
			if (!isset($recurcion[$hash])) $recurcion[$hash] = 0;
			$recurcion[$hash]++;

			$this->events->fireEvent(Events::PERSIST_BEFORE, $entity);
			$this->events->fireEvent($hasId ? Events::PERSIST_BEFORE_UPDATE : Events::PERSIST_BEFORE_INSERT, $entity);

			$relationshipValues = array();
			$fk = $this->getFkForEntity(get_class($entity));
			foreach ($entity->toArray() as $key => $value)
			{
				if (isset($fk[$key]) AND $value instanceof IEntity)
				{
					$repository = $this->getModel()->getRepository($fk[$key]);
					try {
						$repository->persist($value);
					} catch (RecursiveException $re) {
						if (isset($value->id))
						{
							$recursionRelationship[] = array($repository, NULL, $value);
						}
						else
						{
							try {
								$entity->{$key} = NULL;
							} catch (Exception $ree) {
								throw $re;
							}
							$recursionRelationship[] = array($repository, $key, $value);
						}
					}
				}
				else if ($value instanceof IRelationship)
				{
					$relationshipValues[] = $value;;
				}
			}
			if (!$entity->isChanged())
			{
				unset($recurcion[$hash]);
				$recursionRelationshipCallback($entity, $recursionRelationship);
				return $entity;
			}

			if ($id = $this->getMapper()->persist($entity))
			{
				$args = array('id' => $id);
				$this->events->fireEvent(Events::PERSIST, $entity, $args);
				if ($hasId)
				{
					$this->entities[$hasId] = false;
				}
				$id = $entity->id;
				$this->entities[$id] = $entity;

				$recursionRelationshipCallback($entity, $recursionRelationship, true);
				foreach ($relationshipValues as $relationship)
				{
					$relationship->persist();
				}

				$this->events->fireEvent($hasId ? Events::PERSIST_AFTER_UPDATE : Events::PERSIST_AFTER_INSERT, $entity);
				$this->events->fireEvent(Events::PERSIST_AFTER, $entity);
				if ($entity->isChanged())
				{
					$this->getMapper()->persist($entity);
					$this->events->fireEvent(Events::PERSIST, $entity, $args);
				}
				unset($recurcion[$hash]);
				return $entity;
			}
			throw new BadReturnException(array($this->getMapper(), 'persist', 'TRUE', NULL, '; something wrong with mapper'));

		} catch (Exception $e) {
			unset($recurcion[$hash]);
			$recursionRelationshipCallback($entity, $recursionRelationship);
			throw $e;
		}
	}

	/**
	 * Smaze entitu z uloziste {@see IMapper::remove()} a odpoji ji z repository.
	 * Z entitou lze pak jeste pracovat do ukonceni scriptu, ale uz nema id a neni zapojena na repository.
	 *
	 * Vola udalosti:
	 * @see Events::REMOVE_BEFORE
	 * @see Entity::onBeforeRemove()
	 * @see Events::REMOVE_AFTER
	 * @see Entity::onAfterRemove()
	 *
	 * @param scalar|IEntity
	 * @return bool
	 */
	final public function remove($entity)
	{
		$entity = $entity instanceof IEntity ? $entity : $this->getById($entity);
		$this->checkAttachableEntity(get_class($entity), $entity);

		$this->events->fireEvent(Events::REMOVE_BEFORE, $entity);
		if (isset($entity->id))
		{
			if ($this->getMapper()->remove($entity))
			{
				$this->entities[$entity->id] = false;
			}
			else
			{
				throw new BadReturnException(array($this->getMapper(), 'remove', 'TRUE', NULL, '; something wrong with mapper'));
			}
		}
		$this->events->fireEvent(Events::REMOVE_AFTER, $entity);
		return true;
	}

	/**
	 * Primitne vsechny zmeny do uloziste.
	 * Na vsech repozitarich.
	 * @return void
	 * @see IMapper::flush()
	 * @see RepositoryContainer::flush()
	 */
	final public function flush()
	{
		if (func_num_args() > 0 AND func_get_arg(0))
		{
			throw new DeprecatedException(array(__CLASS__, 'flush(TRUE)'));
		}
		return $this->getModel()->flush();
	}

	/**
	 * Ulozit entitu a primitne vsechny zmeny do uloziste.
	 * @see self::persist()
	 * @see self::flush()
	 * @param IEntity
	 * @return IEntity
	 */
	final public function persistAndFlush(IEntity $entity)
	{
		$this->persist($entity);
		$this->flush();
		return $entity;
	}

	/**
	 * Zrusi vsechny zmeny, ale do ukonceni scriptu se zmeny porad drzi.
	 * Na vsech repozitarich.
	 * @todo zrusit i zmeny na entitach, aby se hned vratili do puvodniho stavu.
	 * @return void
	 * @see IMapper::clean()
	 * @see RepositoryContainer::clean()
	 */
	final public function clean()
	{
		if (func_num_args() > 0 AND func_get_arg(0))
		{
			throw new DeprecatedException(array(__CLASS__, 'clean(TRUE)'));
		}
		return $this->getModel()->clean();
	}

	/**
	 * Mapper ktery pouziva tato repository.
	 * @see self::createMapper()
	 * @return DibiMapper |IMapper
	 */
	final public function getMapper()
	{
		if ($this->mapper === NULL)
		{
			$mapper = $this->createMapper();
			if (!($mapper instanceof IMapper))
			{
				if (is_object($mapper))
				{
					throw new BadReturnException('Mapper ' . get_class($mapper) . ' must implement Orm\IMapper');
				}
				throw new BadReturnException(array($this, 'createMapper', 'Orm\IMapper', $mapper));
			}
			$this->mapper = $mapper;
		}
		return $this->mapper;
	}

	/** @return IRepositoryContainer */
	final public function getModel()
	{
		return $this->model;
	}

	/** @return Events */
	final public function getEvents()
	{
		return $this->events;
	}

	/**
	 * Mozno ovlivnit jake entity repository vyraby.
	 * Pri $data === NULL vraci pole nazvu vsech trid ktere tato repository muze vyrobit,
	 * jinak vraci konkretni nazev tridy pro tyto data.
	 * Kdyz vyraby jen jednu tridu muze pokazde vratit string.
	 *
	 * Defaultne vraci nazev repository v jednotem cisle; pro prevod pouziva {@see Inflector::singularize()}.
	 * V pripade potreby je mozne prepsat tuto metodu, nebo property $entityClassName:
	 * <code>
	 * // MiceRepository
	 * protected $entityClassName = 'Mouse';
	 * </code>
	 *
	 * Repository muze vyrabet ruzne entity, muze se rozhodovat na zaklade nejake polozky kterou ma ulozenou v ulozisti, napr. $type
	 * <code>
	 * // ProductsRepository
	 * public function getEntityClassName(array $data = NULL)
	 * {
	 * 	$entities = array(
	 * 		Product::BOOK => 'Book',
	 * 		Product::MAGAZINE => 'Magazine',
	 * 		Product::CD_MUSIC => 'CdMusic',
	 * 		Product::DVD_MOVIE => 'DvdMovie',
	 * 	);
	 *
	 * 	if ($data === NULL) return $entities;
	 * 	else if (isset($entities[$data['type']])) return $entities[$data['type']];
	 * }
	 *
	 * </code>
	 *
	 * Do not call directly.
	 * @param array|NULL
	 * @return string|array
	 */
	public function getEntityClassName(array $data = NULL)
	{
		if ($this->entityClassName === NULL)
		{
			$helper = $this->getModel()->getContext()->getService('repositoryHelper', 'Orm\RepositoryHelper');
			$this->entityClassName = Inflector::singularize($helper->normalizeRepository($this));
		}
		return $this->entityClassName;
	}

	/**
	 * Donacteni parametru do entity.
	 * Do not call directly.
	 * @see Entity::getValue()
	 * @param IEntity
	 * @param string
	 * @return array
	 * @todo refaktorovat
	 */
	public function lazyLoad(IEntity $entity, $param)
	{
		return array();
	}

	/**
	 * Vytvori mapper pro tuto repository.
	 * Defaultne nacita mapper podle jmena `<RepositoryName>Mapper`.
	 * Jinak DibiMapper.
	 * Pro pouziti vlastniho mapper staci jen vytvorit tridu podle konvence, nebo prepsat tuto metodu.
	 * @return DibiMapper |IMapper
	 * @see self::getMapper()
	 */
	protected function createMapper()
	{
		return $this
			->model
			->getContext()
			->getService('mapperFactory', 'Orm\IMapperFactory')
			->createMapper($this)
		;
	}

	/**
	 * Je mozne tuto entitu pripojit do tohoto repository?
	 * @param IEntity
	 * @return bool
	 * @see self::getEntityClassName()
	 */
	final public function isAttachableEntity(IEntity $entity)
	{
		return $this->checkAttachableEntity(get_class($entity), $entity, false);
	}

	/**
	 * @return PerformanceHelper|NULL
	 * @see self::__construct()
	 */
	protected function createPerformanceHelper()
	{
		if (PerformanceHelper::$keyCallback)
		{
			$context = $this->model->getContext();
			if ($context->hasService('performanceHelperCache'))
			{
				return new PerformanceHelper($this, $context->getService('performanceHelperCache', 'ArrayAccess'));
			}
		}
		return NULL;
	}

	/**
	 * Vytvori entity, nebo vrati tuto existujici.
	 * Do not call directly.
	 * @internal
	 *
	 * Vola udalosti:
	 * @see Events::HYDRATE_BEFORE
	 * @see Entity::onLoad()
	 * @see Events::HYDRATE_AFTER
	 *
	 * @param array
	 * @return IEntity
	 * @see self::getEntityClassName()
	 */
	final public function hydrateEntity($data)
	{
		if ($this->conventional === NULL)
		{
			$this->conventional = $this->getMapper()->getConventional(); // speedup
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
			$entityName = $this->getEntityClassName($data);
			$this->checkAttachableEntity($entityName);
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
	 * Call to undefined method.
	 * @param string method name
	 * @param array arguments
	 * @return mixed
	 * @throws MemberAccessException
	 */
	public function __call($name, $args)
	{
		if ($this->mapperAutoCaller === NULL)
		{
			$this->mapperAutoCaller = new MapperAutoCaller($this, $this->getModel()->getContext()->getService('annotationsParser'));
		}
		if ($this->mapperAutoCaller->has($name))
		{
			return call_user_func_array(array($this->getMapper(), $name), $args);
		}
		return parent::__call($name, $args);
	}

	/**
	 * Kontroluje jestli je nazev entity spravny.
	 * @param string
	 * @return void
	 * @throws InvalidEntityException
	 * @see self::isAttachableEntity()
	 * @see self::getEntityClassName()
	 */
	final private function checkAttachableEntity($entityName, IEntity $entity = NULL, $throw = true)
	{
		if ($this->allowedEntities === NULL)
		{
			$allowedEntities = array();
			foreach ((array) $this->getEntityClassName() as $en)
			{
				if (!class_exists($en))
				{
					throw new InvalidEntityException(get_class($this) . ": entity '$en' does not exists; see property Orm\\Repository::\$entityClassName or method Orm\\IRepository::getEntityClassName()");
				}
				$allowedEntities[strtolower($en)] = true;
			}
			$this->allowedEntities = $allowedEntities;
		}
		// todo strtolower mozna bude moc pomale
		if (!isset($this->allowedEntities[strtolower($entityName)]))
		{
			if ($throw)
			{
				$tmp = (array) $this->getEntityClassName();
				$tmpLast = array_pop($tmp);
				$tmp = $tmp ? "'" . implode("', '", $tmp) . "' or '$tmpLast'" : "'$tmpLast'";
				throw new InvalidEntityException(get_class($this) . " can't work with entity '$entityName', only with $tmp");
			}
			return false;
		}
		if ($entity AND $r = $entity->getRepository(false) AND $r !== $this)
		{
			if ($throw)
			{
				throw new InvalidEntityException(EntityHelper::toString($entity) . ' is attached to another repository.');
			}
			return false;
		}
		return true;
	}

	/**
	 * Vrati cizy klice pro tuto entitu.
	 * @param string
	 * @return array paramName => repositoryName
	 */
	final private function getFkForEntity($entityName)
	{
		static $fks = array();
		if (!isset($fks[$entityName]))
		{
			$fk = array();
			foreach (MetaData::getEntityRules($entityName, $this->model) as $name => $rule)
			{
				if ($rule['relationship'] !== MetaData::ManyToOne AND $rule['relationship'] !== MetaData::OneToOne) continue;
				$fk[$name] = $rule['relationshipParam'];
			}
			$fks[$entityName] = $fk;
		}
		return $fks[$entityName];
	}

	/** @deprecated */
	final public function createEntity($data)
	{
		throw new DeprecatedException(array(__CLASS__, 'createEntity()', __CLASS__, 'hydrateEntity()'));
	}

	/** @deprecated */
	final public function isEntity(IEntity $entity)
	{
		throw new DeprecatedException(array(__CLASS__, 'isEntity()', __CLASS__, 'isAttachableEntity()'));
	}

	/** @deprecated */
	final public function getRepositoryName()
	{
		throw new DeprecatedException(array(__CLASS__, 'getRepositoryName()', 'get_class($repository)'));
	}

}

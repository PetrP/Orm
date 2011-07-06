<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use Nette\Object;
use UnexpectedValueException;
use Nette\InvalidStateException;
use Exception;

require_once __DIR__ . '/IRepository.php';
require_once __DIR__ . '/PerformanceHelper.php';
require_once __DIR__ . '/../Entity/EntityHelper.php';

/**
 * Pracuje z entitamy, nezavisle na konretnim ulozisti.
 * Ukladani, mazani, nacitani entit.
 *
 * Pro kazdou entitu (nebo skupinu pribuznych entit) si vytvorte repository.
 *
 * Konvence je pojmenovavat repository mnoznym cisle, a entity jednotnim.
 *
 * <pre
 * class ArticlesRepository extends Repository
 *  ...
 * </pre>
 *
 * Repository se zizkava pres model {@see RepositoryContainer}
 * <pre>
 *
 * $model; // instanceof RepositoryContainer
 * // instanceof ArticlesRepository
 * $model->articles;
 * </pre>
 *
 * Repository je NEZAVISLE NA KONKRETNIM ULOZISTI.
 * O uloziste se stara Mapper {@see DibiMapper} {@see IMapper}
 *
 * Konvence je pojmenovavat metody na vytahovani dat
 * getBy<...>() pro ziskani jednoho zaznamu {@see IEntity}
 * findBy<...>() pro ziskani kolekce zaznamu {@see IEntityCollection}
 * findAll() vsechny zaznamy
 *
 * Na mapperu lze volat metody jako mapper->findByAuthorAndTag($author, $tag) atd
 * Ale na repository je potreba si vsechny vytahovaci metody vytvorit.
 * <pre>
 * public function findByAuthor($author)
 * {
 * 	return $this->mapper->findByAuthor($author);
 * }
 * public function getByName($name)
 * {
 * 	return $this->mapper->getByName($name);
 * }
 * </pre>
 *
 *
 * Defaultne se vytvari entita podle repositoryName v jednotnem cisle ArticlesRepository > Article:
 * @see self::getEntityClassName()
 *
 * Defaultne se pouziva `<RepositoryName>Mapper` nebo DibiMapper:
 * @see self::createMapper()
 *
 * @see self::getById() ziskani zaznamu
 * @see self::persist() ukladani
 * @see self::remove() mazani
 * @see self::flush() promitnuti zmen
 * @see self::clean() zruseni zmen
 *
 */
abstract class Repository extends Object implements IRepository
{

	/** @var IRepositoryContainer */
	private $model;

	/** @var DibiMapper */
	private $mapper;

	/** @var string */
	private $repositoryName;

	/** @var SqlConventional */
	private $conventional;

	/** @var array @todo refaktorovat */
	private $entities = array();

	/** @var PerformanceHelper */
	private $performanceHelper;

	/** @var array cache {@see self::checkEntity() */
	private $allowedEntities;

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
		$repositoryName = strtolower(get_class($this));
		if (substr($repositoryName, -10) === 'repository')
		{
			$repositoryName = substr($repositoryName, 0, strlen($repositoryName) - 10);
		}
		$this->repositoryName = $repositoryName;
		$this->performanceHelper = new PerformanceHelper($this);
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
			throw new UnexpectedValueException("Id must be scalar, '" . (is_object($id) ? 'object ' . get_class($id) : gettype($id)) . "' given");
		}
		$this->performanceHelper->access($id);
		if (isset($this->entities[$id]))
		{
			if ($this->entities[$id]) return $this->entities[$id];
			return NULL;
		}

		// nactu vsechny ktere budu pravdepodobne potrebovat
		if ($ids = $this->performanceHelper->get())
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
	 * @see Entity::onAttach()
	 *
	 * @param IEntity
	 * @return IEntity
	 */
	public function attach(IEntity $entity)
	{
		$this->checkEntity(get_class($entity), $entity);
		if (!$entity->getGeneratingRepository(false))
		{
			$entity->___event($entity, 'attach', $this);
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
	 * @see Entity::onBeforePersist()
	 * @see Entity::onBeforeUpdate() OR Entity::onBeforeInsert()
	 * @see Entity::onPersist()
	 * @see Entity::onAfterUpdate() OR Entity::onAfterInsert()
	 * @see Entity::onAfterPersist()
	 *
	 * @param IEntity
	 * @return IEntity
	 */
	final public function persist(IEntity $entity)
	{
		$this->attach($entity);
		$hasId = isset($entity->id);
		if ($hasId AND !$entity->isChanged())
		{
			return $entity;
		}
		try {
			$hash = spl_object_hash($entity);
			static $recurcion = array();
			if (isset($recurcion[$hash]) AND $recurcion[$hash] > 1)
			{
				throw new InvalidStateException("There is an infinite recursion during persist in " . EntityHelper::toString($entity));
			}
			if (!isset($recurcion[$hash])) $recurcion[$hash] = 0;
			$recurcion[$hash]++;

			$entity->___event($entity, 'beforePersist', $this);
			$entity->___event($entity, $hasId ? 'beforeUpdate' : 'beforeInsert', $this);

			$relationshipValues = array();
			$fk = $this->getFkForEntity(get_class($entity));
			foreach ($entity->toArray() as $key => $value)
			{
				if (isset($fk[$key]) AND $value instanceof IEntity)
				{
					$this->getModel()->getRepository($fk[$key])->persist($value);
				}
				else if ($value instanceof IRelationship)
				{
					$relationshipValues[] = $value;;
				}
			}

			if ($id = $this->getMapper()->persist($entity))
			{
				$entity->___event($entity, 'persist', $this, $id);
				$this->entities[$entity->id] = $entity;

				foreach ($relationshipValues as $relationship)
				{
					$relationship->persist();
				}

				$entity->___event($entity, $hasId ? 'afterUpdate' : 'afterInsert', $this);
				$entity->___event($entity, 'afterPersist', $this);
				if ($entity->isChanged())
				{
					$this->getMapper()->persist($entity);
					$entity->___event($entity, 'persist', $this, $id);
				}
				unset($recurcion[$hash]);
				return $entity;
			}
			throw new InvalidStateException('Something wrong with mapper.');

		} catch (Exception $e) {
			unset($recurcion[$hash]);
			throw $e;
		}
	}

	/**
	 * Smaze entitu z uloziste {@see IMapper::remove()} a odpoji ji z repository.
	 * Z entitou lze pak jeste pracovat do ukonceni scriptu, ale uz nema id a neni zapojena na repository.
	 *
	 * Vola udalosti:
	 * @see Entity::onBeforeRemove()
	 * @see Entity::onAfterRemove()
	 *
	 * @param scalar|IEntity
	 * @return bool
	 */
	final public function remove($entity)
	{
		$entity = $entity instanceof IEntity ? $entity : $this->getById($entity);
		$this->checkEntity(get_class($entity), $entity);

		$entity->___event($entity, 'beforeRemove', $this);
		if (isset($entity->id))
		{
			if ($this->getMapper()->remove($entity))
			{
				$this->entities[$entity->id] = false;
			}
			else
			{
				throw new InvalidStateException('Something wrong with mapper.');
			}
		}
		$entity->___event($entity, 'afterRemove', $this);
		return true;
	}

	/**
	 * Primitne vsechny zmeny do uloziste.
	 * @param bool true jenom pro tuto repository; false pro vsechny repository
	 * @return void
	 * @see IMapper::flush()
	 * @see RepositoryContainer::flush()
	 */
	final public function flush($onlyThis = false)
	{
		if ($onlyThis) return $this->getMapper()->flush();
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
	 * @todo zrusit i zmeny na entitach, aby se hned vratili do puvodniho stavu.
	 * @param bool true jenom pro tuto repository; false pro vsechny repository
	 * @return void
	 * @see IMapper::clean()
	 * @see RepositoryContainer::clean()
	 */
	final public function clean($onlyThis = false)
	{
		if ($onlyThis) return $this->getMapper()->rollback();
		return $this->getModel()->clean();
	}

	/**
	 * Nazev repository. Vetsinou lowercase nazev tridy bez sufixu Repository
	 * @return string
	 */
	final public function getRepositoryName()
	{
		return $this->repositoryName;
	}

	/**
	 * Mapper ktery pouziva tato repository.
	 * @see self::createMapper()
	 * @return DibiMapper |IMapper
	 */
	final public function getMapper()
	{
		if (!isset($this->mapper))
		{
			$mapper = $this->createMapper();
			if (!($mapper instanceof IMapper))
			{
				if (is_object($mapper))
				{
					throw new InvalidStateException('Mapper ' . get_class($mapper) . ' must implement Orm\IMapper');
				}
				throw new InvalidStateException(get_class($this) . "::createMapper() must return Orm\\IMapper, '" . gettype($mapper) . "' given");
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

	/**
	 * Mozno ovlivnit jake entity repository vyraby.
	 * Pri $data === NULL vraci pole nazvu vsech trid ktere tato repository muze vyrobit,
	 * jinak vraci konkretni nazev tridy pro tyto data.
	 * Kdyz vyraby jen jednu tridu muze pokazde vratit string.
	 *
	 * Defaultne vraci nazev repository v jednotem cisle, ale hloupe jen bez s na konci.
	 * V pripade nepravidelnosti je mozne prepsat tuto metodu, nebo property entityClassName:
	 * <pre>
	 * // CitiesRepository
	 * protected $entityClassName = 'City';
	 * </pre>
	 *
	 * Repository muze vyrabet ruzne entity, muze se rozhodovat na zaklade nejake polozky kterou ma ulozenou v ulozisti, napr. $type
	 * <pre>
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
	 * </pre>
	 *
	 * Do not call directly.
	 * @param array|NULL
	 * @return string|array
	 */
	public function getEntityClassName(array $data = NULL)
	{
		if (isset($this->entityClassName)) return (string) $this->entityClassName;
		return rtrim($this->getRepositoryName(), 's');
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
		$class = $this->getRepositoryName() . 'Mapper';
		if (class_exists($class))
		{
			return new $class($this);
		}
		return new DibiMapper($this);
	}

	/**
	 * Je mozne tuto entitu ulozit do tohoto repository?
	 * @param IEntity
	 * @return bool
	 * @see self::getEntityClassName()
	 */
	final public function isEntity(IEntity $entity) // todo rename
	{
		return $this->checkEntity(get_class($entity), $entity, false);
	}

	/**
	 * Vytvori entity, nebo vrati tuto existujici.
	 * Do not call directly.
	 * @internal
	 *
	 * Vola udalosti:
	 * @see Entity::onLoad()
	 *
	 * @param array
	 * @return IEntity
	 * @see self::getEntityClassName()
	 */
	final public function createEntity($data) // todo rename
	{
		$id = isset($data['id']) ? $data['id'] : NULL;
		if (!$id)
		{
			throw new InvalidStateException("Data, that is returned from storage, doesn't contain id.");
		}
		if (!isset($this->entities[$id]) OR !$this->entities[$id])
		{
			if (!isset($this->conventional))
			{
				$this->conventional = $this->getMapper()->getConventional(); // speedup
			}
			$data = (array) $this->conventional->formatStorageToEntity($data);
			$entityName = $this->getEntityClassName($data);
			$this->checkEntity($entityName);
			$entity = unserialize("O:".strlen($entityName).":\"$entityName\":0:{}");
			if (!($entity instanceof IEntity)) throw new InvalidStateException();
			$entity->___event($entity, 'load', $this, $data);
			$this->entities[$id] = $entity;
		}
		return $this->entities[$id];
	}

	/**
	 * Kontroluje jestli je nazev entity spravny.
	 * @param string
	 * @return void
	 * @throws UnexpectedValueException
	 * @see self::isEntity()
	 * @see self::getEntityClassName()
	 */
	final private function checkEntity($entityName, IEntity $entity = NULL, $throw = true)
	{
		if (!isset($this->allowedEntities))
		{
			$allowedEntities = array();
			foreach ((array) $this->getEntityClassName() as $en)
			{
				if (!class_exists($en))
				{
					throw new UnexpectedValueException(get_class($this) . ": entity '$en' does not exists; see property Orm\\Repository::\$entityClassName or method Orm\\IRepository::getEntityClassName()");
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
				throw new UnexpectedValueException(get_class($this) . " can't work with entity '$entityName', only with $tmp");
			}
			return false;
		}
		if ($entity AND $r = $entity->getGeneratingRepository(false) AND $r !== $this)
		{
			if ($throw)
			{
				throw new UnexpectedValueException(EntityHelper::toString($entity) . ' is attached to another repository.');
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

}

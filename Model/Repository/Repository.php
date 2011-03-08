<?php

require_once dirname(__FILE__) . '/IRepository.php';

require_once dirname(__FILE__) . '/PerformanceHelper.php';

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
 * Repository se zizkava pres model {@see Model}
 * <pre>
 *
 * $model; // instanceof Model
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

	/** @var Model */
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

	/** @var array cache {@see self::checkEntityName() */
	private $allowedEntities;

	/**
	 * @var string
	 * @see self::getEntityClassName()
	 */
	protected $entityClassName;

	/**
	 * @param string
	 * @param Model
	 */
	public function __construct($repositoryName, Model $model)
	{
		$this->model = $model;
		$this->repositoryName = $repositoryName;
		$this->conventional = $this->getMapper()->getConventional(); // speedup
		$this->performanceHelper = new PerformanceHelper($this);
	}

	/**
	 * @param int
	 * @return IEntity
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

	/**
	 * Ulozit entitu {@see IMapper::persist()} a zapoji ji do repository.
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
		$this->checkEntityName(get_class($entity));
		$hasId = isset($entity->id);
		if ($hasId AND !$entity->isChanged())
		{
			return $entity;
		}
		$entity->___event($entity, 'beforePersist', $this);
		$entity->___event($entity, $hasId ? 'beforeUpdate' : 'beforeInsert', $this);

		$relationshipValues = array();
		$fk = $this->getFkForEntity(get_class($entity));
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
			}
			return $entity;
		}
		throw new Exception(); // todo
	}

	/**
	 * Smaze entitu z uloziste {@see IMapper::remove()} a odpoji ji z repository.
	 * Z entitou lze pak jeste pracovat do ukonceni scriptu, ale uz nema id a neni zapojena na repository.
	 *
	 * Vola udalosti:
	 * @see Entity::onBeforeRemove()
	 * @see Entity::onAfterRemove()
	 *
	 * @param int|IEntity
	 * @return bool
	 */
	final public function remove($entity) // todo prejmenovat na remove?
	{
		$entity = $entity instanceof IEntity ? $entity : $this->getById($entity);
		$this->checkEntityName(get_class($entity));

		$entity->___event($entity, 'beforeRemove', $this);
		if (isset($entity->id))
		{
			if ($this->getMapper()->remove($entity))
			{
				unset($this->entities[$entity->id]);
			}
			else
			{
				throw new Exception(); // todo
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
	 * @see Model::flush()
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
	 * @see Model::clean()
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
				throw new InvalidStateException();
			}
			$this->mapper = $mapper;
		}
		return $this->mapper;
	}

	/** @return Model */
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
		$class{0} = $class{0} & "\xDF"; // ucfirst
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
		try {
			$this->checkEntityName(get_class($entity));
		} catch (Exception $e) {
			return false;
		}
		return true;
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
		if (!isset($this->entities[$data['id']]))
		{
			$data = (array) $this->conventional->formatStorageToEntity($data);
			$entityName = $this->getEntityClassName($data);
			$this->checkEntityName($entityName);
			$entity = unserialize("O:".strlen($entityName).":\"$entityName\":0:{}");
			if (!($entity instanceof IEntity)) throw new InvalidStateException();
			$entity->___event($entity, 'load', $this, $data);
			$this->entities[$data['id']] = $entity;
		}
		return $this->entities[$data['id']];
	}

	/**
	 * Kontroluje jestli je nazev entity spravny.
	 * @param string
	 * @return void
	 * @throws UnexpectedValueException
	 * @see self::isEntity()
	 * @see self::getEntityClassName()
	 */
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
			foreach (MetaData::getEntityRules($entityName) as $name => $rule)
			{
				if ($rule['relationship'] !== MetaData::ManyToOne AND $rule['relationship'] !== MetaData::OneToOne) continue;
				$fk[$name] = $rule['relationshipParam'];
			}
			$fks[$entityName] = $fk;
		}
		return $fks[$entityName];
	}


	/** @ignore @deprecated @see self::getEntityClassName() */
	final public function getEntityName(array $data = NULL)
	{
		throw new DeprecatedException();
	}
	/** @ignore @deprecated @see self::remove() */
	final public function delete($entity)
	{
		return $this->remove($entity);
	}

}

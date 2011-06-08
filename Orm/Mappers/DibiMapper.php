<?php

namespace Orm;

use Nette\NotImplementedException;
use Nette\NotSupportedException;
use Nette\InvalidStateException;
use Dibi;
use DibiConnection;
use DibiTranslator;
use ReflectionMethod;
use stdClass;

require_once __DIR__ . '/Mapper.php';
require_once __DIR__ . '/DibiPersistenceHelper.php';
require_once __DIR__ . '/Conventional/SqlConventional.php';
require_once __DIR__ . '/Collection/DibiCollection.php';

/**
 * @property-read DibiConnection $connection
 */
class DibiMapper extends Mapper
{

	/** @var DibiConnection @see self::getConnection() */
	private $connection;

	/** @var array @see self::getJoinInfo() */
	private $joinInfoCache = array('cache' => array(), 'fk' => NULL);

	/** @var array @see self::begin() */
	private static $transactions = array();

	/**
	 * Vsechny entity.
	 * Musi vratit skutecne vsechny entity.
	 * Zadna jina metoda nesmi vratit nejakou entitu kterou by nevratila tato.
	 * @return IEntityCollection
	 */
	public function findAll()
	{
		list($class, $classInfo) = $this->getCollectionClass(true);
		if ($classInfo === 'dibi')
		{
			return new $class($this->getTableName(), $this->getConnection(), $this->getRepository());
		}
		else if ($classInfo === 'datasource')
		{
			return $this->dataSource($this->getTableName());
		}
		else
		{
			throw new NotImplementedException();
		}
	}

	/**
	 * @param scalar
	 * @return IEntity|NULL
	 * @todo vynocovat na IMapper?
	 */
	public function getById($id)
	{
		if (!$id) return NULL;
		return $this->findAll()->where('[id] = %s', $id)->applyLimit(1)->fetch();
	}

	/**
	 * @see IRepository::persist()
	 * @param IEntity
	 * @return scalar id
	 */
	public function persist(IEntity $entity)
	{
		$this->begin();
		return $this->getPersistenceHelper()->persist($entity);
	}

	/**
	 * @see IRepository::remove()
	 * @param IEntity
	 * @return bool
	 */
	public function remove(IEntity $entity)
	{
		$this->begin();
		return (bool) $this->getConnection()->delete($this->getTableName())->where('[id] = %s', $entity->id)->execute();
	}

	/**
	 * @see IRepository::flush()
	 * @return void
	 */
	public function flush()
	{
		$connection = $this->getConnection();
		$hash = spl_object_hash($connection);
		if (isset(self::$transactions[$hash]))
		{
			$connection->commit();
			unset(self::$transactions[$hash]);
		}
	}

	/**
	 * @see IRepository::clean()
	 * @return void
	 */
	final public function rollback()
	{
		$connection = $this->getConnection();
		$hash = spl_object_hash($connection);
		if (isset(self::$transactions[$hash]))
		{
			$connection->rollback();
			// todo zmeny zustanou v Repository::$entities
			unset(self::$transactions[$hash]);
		}
	}

	/**
	 * @see ManyToMany::getMapper()
	 * @param string
	 * @param IRepository
	 * @param string
	 * @return IManyToManyMapper
	 */
	public function createManyToManyMapper($firstParam, IRepository $repository, $secondParam)
	{
		$mapper = new DibiManyToManyMapper($this->getConnection());
		$c = $this->getConventional();
		$mapper->table = $c->getManyToManyTable($this->getRepository(), $repository);
		$mapper->firstParam = $c->getManyToManyParam($firstParam);
		$mapper->secondParam = $c->getManyToManyParam($secondParam);;
		return $mapper;
	}

	/**
	 * @see self::createConnection()
	 * @return DibiConnection
	 */
	final public function getConnection()
	{
		if (!isset($this->connection))
		{
			$connection = $this->createConnection();
			if (!($connection instanceof DibiConnection))
			{
				throw new InvalidStateException(get_class($this) . "::createConnection() must return DibiConnection, '" . (is_object($connection) ? get_class($connection) : gettype($connection)) . "' given");
			}
			$this->connection = $connection;
		}
		return $this->connection;
	}

	/**
	 * @see self::getConnection()
	 * @return DibiConnection
	 */
	protected function createConnection()
	{
		return dibi::getConnection();
	}

	/**
	 * @see self::getConventional()
	 * @return IConventional
	 */
	protected function createConventional()
	{
		return new SqlConventional($this);
	}

	/**
	 * @see self::persist()
	 * @return DibiPersistenceHelper
	 */
	protected function getPersistenceHelper()
	{
		$h = new DibiPersistenceHelper;
		$h->connection = $this->getConnection();
		$h->conventional = $this->getConventional();
		$h->table = $this->getTableName();
		$h->mapper = $this;
		return $h;
	}

	/**
	 * Zahaji transakci.
	 * Vola se pri kazde operaci. Jen pri prvnim zavolani se transakce otevira.
	 * @see self::persist()
	 * @see self::remove()
	 */
	final protected function begin()
	{
		$connection = $this->getConnection();
		$hash = spl_object_hash($connection);
		if (!isset(self::$transactions[$hash]))
		{
			$connection->begin();
			self::$transactions[$hash] = true;
		}
	}

	/**
	 * <pre>
	 * 	$mapper->dataSource('SELECT foo, bar FROM table WHERE [bar] = %i', 3);
	 * </pre>
	 * @param string
	 * @return DataSourceCollection
	 */
	protected function dataSource()
	{
		list($class, $classInfo) = $this->getCollectionClass(true);
		if ($class === 'Orm\DibiCollection')
		{
			// bc, i kdyz se pouziva DibiCollection tak dataSource muze fungovat, kdyz se nepouziva custom collection
			$class = 'Orm\DataSourceCollection';
		}
		else if ($classInfo !== 'datasource')
		{
			throw new NotSupportedException();
		}

		static $dibiTranslatorVersion; // pro bc se starsi dibi
		if ($dibiTranslatorVersion === NULL)
		{
			$dibiTranslatorVersion = 'driver';
			$r = new ReflectionMethod('DibiTranslator', '__construct');
			$p = $r->getParameters();
			if (current($p)->name === 'connection')
			{
				$dibiTranslatorVersion = 'connection';
			}
		}

		$connection = $this->getConnection();
		$connection->getDriver(); // v novem dibi se tady connectne
		if (!$connection->isConnected())
		{
			// @codeCoverageIgnoreStart
			$connection->sql(''); // protoze nema public metodu DibiConnection::connect()
		}	// @codeCoverageIgnoreEnd
		$translator = new DibiTranslator($dibiTranslatorVersion === 'connection' ? $connection : $connection->getDriver());
		$args = func_get_args();
		return new $class($translator->translate($args), $connection, $this->getRepository());
	}

	/**
	 * Vraci nazev tridy kterou tento mapper pouziva jako IEntityCollection
	 * @see self::getCollectionClass()
	 * @return string
	 */
	protected function createCollectionClass()
	{
		return 'Orm\DibiCollection';
	}

	/**
	 * Nazev tabulky
	 * @return string
	 */
	protected function getTableName()
	{
		return $this->getConventional()->getTable($this->getRepository());
	}

	/**
	 * @internal
	 * @param string author->lastName or author->group->name
	 * @return object
	 * 	->key author.last_name
	 *  ->joins[] array(
	 * 		alias => author
	 * 	?	sourceKey => author
	 * 		xConventionalKey => author_id
	 * 		yConventionalKey => id
	 * 		table => users
	 * 		findBy => array
	 * 	?	mapper => DibiMapper
	 * 	?	conventional => IConventional
	 * )
	 * Work with all propel defined association.
	 * @todo refactor
	 */
	public function getJoinInfo($key, stdClass $result = NULL)
	{
		if (strpos($key, '->') === false)
		{
			return NULL;
		}
		if (!$result)
		{
			$result = (object) array('key' => NULL, 'joins' => array());
		}
		$lastJoin = end($result->joins);

		$tmp = explode('->', $key, 3);
		$sourceKey = $tmp[0];
		$targetKey = $tmp[1];
		$next = isset($tmp[2]) ? $tmp[2] : NULL;

		if (!isset($this->joinInfoCache['cache'][$sourceKey]))
		{
			$mappper = $this;
			$conventional = $this->getConventional();
			$model = $this->getModel();
			if ($this->joinInfoCache['fk'] === NULL)
			{
				foreach ((array) $this->getRepository()->getEntityClassName() as $entityName)
				{
					foreach (MetaData::getEntityRules($entityName, $this->getModel()) as $name => $rule)
					{
						if ($rule['relationship'] === MetaData::OneToMany)
						{
							$loader = (array) $rule['relationshipParam']; // hack
							$r = $loader["\0Orm\\RelationshipLoader\0repository"];
							$p = $loader["\0Orm\\RelationshipLoader\0param"];
							if ($r AND $p)
							{
								$this->joinInfoCache['fk'][$name] = array($r, 'id', $p);
							}
						}
						else if ($rule['relationship'] === MetaData::ManyToMany)
						{
							$loader = (array) $rule['relationshipParam']; // hack
							$r = $loader["\0Orm\\RelationshipLoader\0repository"];
							$p = $loader["\0Orm\\RelationshipLoader\0param"];
							if ($r AND $p)
							{
								$parentRepository = $this->getRepository();
								$childRepository = $this->getModel()->getRepository($r);
								$childParam = $loader["\0Orm\\RelationshipLoader\0param"];
								$parentParam = $loader["\0Orm\\RelationshipLoader\0parentParam"];
								if ($loader["\0Orm\\RelationshipLoader\0mappedByThis"])
								{
									$manyToManyMapper = $this->createManyToManyMapper($parentParam, $childRepository, $childParam);
									$parentParam = $manyToManyMapper->firstParam;
									$childParam = $manyToManyMapper->secondParam;
								}
								else
								{
									$manyToManyMapper = $childRepository->getMapper()->createManyToManyMapper($childParam, $parentRepository, $parentParam);
									$parentParam = $manyToManyMapper->secondParam;
									$childParam = $manyToManyMapper->firstParam;
								}
								$this->joinInfoCache['fk'][$name] = array($r, $childParam, 'id', array($manyToManyMapper->table, 'id', $parentParam));
							}
						}
						else
						{
							$this->joinInfoCache['fk'][$name] = array($rule['relationshipParam'], NULL, 'id');
						}
					}
				}
			}
			if (!isset($this->joinInfoCache['fk'][$sourceKey]))
			{
				throw new InvalidStateException(get_class($this->getRepository()) . ": neni zadna vazba na `$sourceKey`");
			}
			$manyToManyJoin = NULL;
			if (isset($this->joinInfoCache['fk'][$sourceKey][3]))
			{
				$manyToManyJoin = $this->joinInfoCache['fk'][$sourceKey][3];
				$manyToManyJoin = array(
					'alias' => 'm2m__' . $sourceKey,
					'xConventionalKey' => $manyToManyJoin[1],
					'yConventionalKey' => $manyToManyJoin[2],
					'table' => $manyToManyJoin[0],
					'findBy' => array(),
				);
			}
			$joinRepository = $model->getRepository($this->joinInfoCache['fk'][$sourceKey][0]);
			$tmp['mapper'] = $joinRepository->getMapper();
			if (!($tmp['mapper'] instanceof DibiMapper))
			{
				throw new InvalidStateException(get_class($joinRepository) . " ($sourceKey) nepouziva Orm\\DibiMapper, data nelze propojit.");
			}
			$tmp['conventional'] = $tmp['mapper']->getConventional();
			$tmp['table'] = $tmp['mapper']->getTableName();
			// todo brat table z collection?
			if ($tmp['mapper']->getConnection() !== $this->connection)
			{
				// todo porovnavat connection na collection?
				throw new InvalidStateException(get_class($joinRepository) . " ($sourceKey) pouziva jiny Orm\\DibiConnection nez " . get_class($this->getRepository()) . ", data nelze propojit.");
			}
			$tmp['sourceKey'] = $sourceKey;
			$cTmp = $conventional->formatEntityToStorage(array($this->joinInfoCache['fk'][$sourceKey][1] === NULL ? $sourceKey : $this->joinInfoCache['fk'][$sourceKey][1] => NULL));
			$tmp['xConventionalKey'] = key($cTmp);
			$cTmp = $tmp['conventional']->formatEntityToStorage(array($this->joinInfoCache['fk'][$sourceKey][2] => NULL));
			$tmp['yConventionalKey'] = key($cTmp);
			$tmp['alias'] = $sourceKey;

			$collection = $tmp['mapper']->findAll();
			if (!($collection instanceof DibiCollection))
			{
				throw new InvalidStateException(get_class($joinRepository) . " ($sourceKey) nepouziva Orm\\DibiCollection, data nelze propojit.");
			}
			$collectionArray = (array) $collection; // hack
			if ($collectionArray["\0*\0where"])
			{
				throw new InvalidStateException(get_class($joinRepository) . " ($sourceKey) Orm\\DibiCollection pouziva where(), data nelze propojit.");
			}
			$tmp['findBy'] = $collectionArray["\0*\0findBy"];

			$this->joinInfoCache['cache'][$sourceKey] = $manyToManyJoin ? array($manyToManyJoin, $tmp) : array($tmp);
		}

		foreach ($this->joinInfoCache['cache'][$sourceKey] as $join)
		{
			if ($lastJoin)
			{
				$join['alias'] = $lastJoin['alias'] . '->' . $join['alias'];
			}
			$result->joins[] = $join;
		}

		if ($next)
		{
			$result = $join['mapper']->getJoinInfo($targetKey . '->' . $next, $result);
		}
		else
		{
			$tmp = $join['conventional']->formatEntityToStorage(array($targetKey => NULL));
			$targetConventionalKey = key($tmp);
			$result->key = $join['alias'] . '.' . $targetConventionalKey;
		}
		return $result;
	}
}

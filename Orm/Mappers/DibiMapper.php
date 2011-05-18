<?php

namespace Orm;

use Nette\NotImplementedException;
use Nette\NotSupportedException;
use Nette\InvalidStateException;
use Nette\Object;
use Dibi;
use DibiConnection;
use DibiTranslator;
use DibiException;
use ArrayObject;
use DateTime;
use stdClass;

require_once dirname(__FILE__) . '/Mapper.php';

require_once dirname(__FILE__) . '/Conventional/SqlConventional.php';

require_once dirname(__FILE__) . '/Collection/DibiCollection.php';


/**
 * @property-read DibiConnection $connection
 */
class DibiMapper extends Mapper
{
	private $connection;

	/** @var array @see self::getJoinInfo() */
	private $joinInfoCache = array('cache' => array(), 'fk' => NULL);

	public function getConnection()
	{
		if (!($this->connection instanceof DibiConnection))
		{
			$this->connection = $this->createConnection();
		}
		return $this->connection;
	}

	protected function createConnection()
	{
		return dibi::getConnection();
	}

	protected function createConventional()
	{
		return new SqlConventional($this);
	}


	public function findAll()
	{
		list($class, $classInfo) = $this->getCollectionClass(true);
		if ($classInfo === 'dibi')
		{
			return new $class($this->getTableName(), $this->getConnection(), $this->repository);
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

	protected function getPersistenceHelper()
	{
		$h = new DibiPersistenceHelper;
		$h->connection = $this->getConnection();
		$h->conventional = $this->getConventional();
		$h->table = $this->getTableName();
		$h->mapper = $this;
		return $h;
	}

	private static $transactions = array();

	final public function begin()
	{
		$connection = $this->getConnection();
		$hash = spl_object_hash($connection);
		if (!isset(self::$transactions[$hash]))
		{
			$connection->begin();
			self::$transactions[$hash] = true;
		}
	}

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

	public function persist(IEntity $entity)
	{
		$this->begin();
		return $this->getPersistenceHelper()->persist($entity);
	}


	public function remove(IEntity $entity)
	{
		$this->begin();
		return (bool) $this->getConnection()->delete($this->getTableName())->where('[id] = %s', $entity->id)->execute();
	}

	public function flush()
	{
		$this->getConnection()->commit();
	}

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

		$connection = $this->getConnection();
		$args = func_get_args();
		$connection->driver;
		if (!$connection->isConnected())
			$connection->sql(''); // protoze nema public metodu DibiConnection::connect()
		static $dibiTranslatorVersion;
		if ($dibiTranslatorVersion === NULL)
		{
			$dibiTranslatorVersion = 'driver';
			$r = new MethodReflection('DibiTranslator', '__construct');
			if (current($r->getParameters())->name === 'connection')
			{
				$dibiTranslatorVersion = 'connection';
			}
		}
		$translator = new DibiTranslator($dibiTranslatorVersion === 'connection' ? $connection : $connection->getDriver());
		return new $class($translator->translate($args), $connection, $this->repository);
	}

	protected function createCollectionClass()
	{
		return 'Orm\DibiCollection';
	}

	public function getById($id)
	{
		if (!$id) return NULL;
		return $this->findAll()->where('[id] = %s', $id)->applyLimit(1)->fetch();
	}

	protected function getTableName()
	{
		return $this->repository->getRepositoryName();
	}

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
				foreach ((array) $this->repository->getEntityClassName() as $entityName)
				{
					foreach (MetaData::getEntityRules($entityName) as $name => $rule)
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
				throw new InvalidStateException(get_class($this->repository) . ": neni zadna vazba na `$sourceKey`");
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
				throw new InvalidStateException(get_class($joinRepository) . " ($sourceKey) pouziva jiny Orm\\DibiConnection nez " . get_class($this->repository) . ", data nelze propojit.");
			}
			$tmp['sourceKey'] = $sourceKey;
			$tmp['xConventionalKey'] = key($conventional->formatEntityToStorage(array($this->joinInfoCache['fk'][$sourceKey][1] === NULL ? $sourceKey : $this->joinInfoCache['fk'][$sourceKey][1] => NULL)));
			$tmp['yConventionalKey'] = key($tmp['conventional']->formatEntityToStorage(array($this->joinInfoCache['fk'][$sourceKey][2] => NULL)));
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
			$targetConventionalKey = key($join['conventional']->formatEntityToStorage(array($targetKey => NULL)));
			$result->key = $join['alias'] . '.' . $targetConventionalKey;
		}
		return $result;
	}
}
// todo refactor
class DibiPersistenceHelper extends Object
{
	public $table;
	public $connection;
	public $conventional;
	public $mapper;

	public $witchParams = NULL;
	public $witchParamsNot = NULL;

	public function persist(IEntity $entity, $id = NULL)
	{
		$values = $entity->toArray();
		if ($id !== NULL) $values['id'] = $id;

		foreach ($values as $key => $value)
		{
			if ($key !== 'id' AND (($this->witchParams !== NULL AND !in_array($key, $this->witchParams)) OR ($this->witchParamsNot !== NULL AND in_array($key, $this->witchParamsNot))))
			{
				unset($values[$key]);
				continue;
			}
			if ($value instanceof IEntityInjection)
			{
				$values[$key] = $value = $value->getInjectedValue();
			}

			if ($value instanceof IEntity)
			{
				$values[$key] = $value->id;
			}
			else if (is_array($value) OR ($value instanceof ArrayObject AND get_class($value) == 'ArrayObject'))
			{
				$values[$key] = serialize($value); // todo zkontrolovat jestli je jednodimenzni a neobrahuje zadne nesmysly
			}
			else if ($value instanceof IRelationship)
			{
				unset($values[$key]);
			}
			else if (is_object($value) AND method_exists($value, '__toString'))
			{
				$values[$key] = $value->__toString();
			}
			else if ($value !== NULL AND !($value instanceof DateTime) AND !is_scalar($value))
			{
				throw new InvalidStateException("Neumim ulozit `".get_class($entity)."::$$key` " . (is_object($value) ? get_class($value) : gettype($value)));
			}
		}

		$values = $this->conventional->formatEntityToStorage($values);
		$table = $this->table;

		if (method_exists($this->connection->driver, 'getReflector'))
		{
			$columns = $this->connection->driver->getReflector()->getColumns($table);
		}
		else
		{
			$columns = $this->connection->driver->getColumns($table);
		}
		// todo inline cache

		$tmp = array();
		foreach ($columns as $column)
		{
			if (array_key_exists($column['name'], $values))
			{
				$tmp[$column['name']] = $values[$column['name']];
			}
			// todo else nejaky zpusob jak rict o chybe, protoze nekdy to chyba byt muze, jindy ale ne
		}
		// todo dalsi ktere nejsou v tabulce muze byt taky chyba (ale nemusi)
		// todo vytvorit moznost zkontrolovat db, kde se budou kontrolovat jestli nejaky radek nechybi, nebo naopak nepribyva, jestli nekte nechyby NULL (nebo naopak), mozna i zkontrolovat default hodnoy, a typy
		$values = $tmp;

		if (isset($entity->id) AND $this->connection->fetch('SELECT [id] FROM %n WHERE [id] = %s', $table, $entity->id))
		{
			$id = $entity->id;
			$this->connection->update($table, $values)->where('[id] = %s', $id)->execute();
		}
		else
		{
			if (array_key_exists('id', $values) AND $values['id'] === NULL) unset($values['id']);
			$this->connection->insert($table, $values)->execute();
			try {
				$id = $this->connection->getInsertId();
			} catch (DibiException $e) {
				if (isset($values['id'])) $id = $values['id'];
				else if (isset($entity->id)) $id = $entity->id;
				else throw $e;
			}
		}

		return $id;
	}

}

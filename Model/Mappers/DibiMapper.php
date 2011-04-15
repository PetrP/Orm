<?php

require_once dirname(__FILE__) . '/Mapper.php';

require_once dirname(__FILE__) . '/Conventional/SqlConventional.php';

require_once dirname(__FILE__) . '/Collection/DibiCollection.php';


/**
 * @property-read DibiConnection $connection
 */
class DibiMapper extends Mapper
{
	private $connection;


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
		if ($this->createCollectionClass() === 'DataSourceCollection') // todo
		{
			return new DibiCollection($this->getTableName(), $this->getConnection(), $this->repository);
		}
		return $this->dataSource($this->getTableName());
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
		return (bool) $this->getConnection()->delete($this->getTableName())->where('[id] = %i', $entity->id)->execute();
	}

	public function flush()
	{
		$this->getConnection()->commit();
	}

	protected function dataSource()
	{
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
		$class = $this->getCollectionClass();
		return new $class($translator->translate($args), $connection, $this->repository);
	}

	protected function createCollectionClass()
	{
		return 'DataSourceCollection';
	}

	public function getById($id)
	{
		if (!$id) return NULL;
		return $this->findAll()->where('[id] = %i', $id)->applyLimit(1)->fetch();
	}

	protected function getTableName()
	{
		return $this->repository->getRepositoryName();
	}

	public function createDefaultManyToManyMapper()
	{
		$c = $this->getConnection();
		return new DibiManyToManyMapper($c);
	}

	/**
	 * @internal
	 * @param string author->lastName or author->group->name
	 * @return object
	 * 	->key author.last_name
	 *  ->joins[] array(
	 * 		alias => author
	 * 	?	sourceKey => author
	 * 		sourceConventionalKey => author_id
	 * 	?	targetKey => lastName
	 * 	?	targetConventionalKey => last_name
	 * 		table => users
	 * )
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

		$cache = array(); // todo
		static $cacheFk;
		if (!isset($cache[$sourceKey]))
		{
			$mappper = $this;
			$conventional = $this->getConventional();
			$model = $this->getModel();
			if ($cacheFk === NULL)
			{
				foreach ((array) $this->repository->getEntityClassName() as $entityName)
				{
					foreach (MetaData::getEntityRules($entityName) as $name => $rule)
					{
						if ($rule['relationship'] !== MetaData::ManyToOne AND $rule['relationship'] !== MetaData::OneToOne) continue;
						$cacheFk[$name] = $rule['relationshipParam'];
					}
				}
			}
			if (!isset($cacheFk[$sourceKey]))
			{
				throw new InvalidStateException(get_class($this->repository) . ": neni zadna vazba na `$sourceKey`");
			}
			$tmp['repository'] = $model->getRepository($cacheFk[$sourceKey]);
			$tmp['mapper'] = $tmp['repository']->getMapper();
			if (!($tmp['mapper'] instanceof DibiMapper))
			{
				throw new InvalidStateException(get_class($tmp['repository']) . " ($sourceKey) nepouziva DibiMapper, data nelze propojit.");
			}
			$tmp['conventional'] = $tmp['mapper']->getConventional();
			$tmp['connection'] = $tmp['mapper']->getConnection();
			$tmp['table'] = $tmp['mapper']->getTableName();
			if ($tmp['connection'] !== $this->connection)
			{
				throw new InvalidStateException(get_class($tmp['repository']) . " ($sourceKey) pouziva jiny DibiConnection nez " . get_class($this->repository) . ", data nelze propojit.");
			}
			$tmp['sourceKey'] = $sourceKey;
			$tmp['sourceConventionalKey'] = key($conventional->formatEntityToStorage(array($sourceKey => NULL)));
			$tmp['targetKey'] = $targetKey;
			$tmp['targetConventionalKey'] = key($tmp['conventional']->formatEntityToStorage(array($targetKey => NULL)));
			$tmp['alias'] = $tmp['sourceKey'];
			$cache[$sourceKey] = $tmp;
		}

		$join = $cache[$sourceKey];

		if ($lastJoin)
		{
			$join['alias'] = $lastJoin['alias'] . '~' . $join['alias'];
		}

		$result->joins[] = $join;
		if ($next)
		{
			$result = $join['mapper']->getJoinInfo($targetKey . '->' . $next, $result);
		}
		else
		{
			$result->key = $join['alias'] . '.' . $join['targetConventionalKey'];
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

		if (isset($entity->id) AND $this->connection->fetch('SELECT [id] FROM %n WHERE [id] = %i', $table, $entity->id))
		{
			$id = $entity->id;
			$this->connection->update($table, $values)->where('[id] = %i', $id)->execute();
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

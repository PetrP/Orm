<?php

require_once dirname(__FILE__) . '/Mapper.php';

require_once dirname(__FILE__) . '/Conventional/SqlConventional.php';

require_once dirname(__FILE__) . '/DataSource/DibiMapperDataSource.php';


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
		return $this->dataSource($this->getTableName());
	}

	protected function findBy(array $where)
	{
		$all = $this->findAll();
		$where = $this->getConventional()->formatEntityToStorage($where);
		// todo instanceof IModelDataSource
		foreach ($where as $key => $value)
		{
			if (is_array($value))
			{
				$all->where('%n IN %in', $key, $value);
			}
			else if ($value === NULL)
			{
				$all->where('%n IS NULL', $key);
			}
			else
			{
				$all->where('%n = %s', $key, $value instanceof Entity ? $value->id : $value);
			}
		}
		return $all;
	}

	protected function getBy(array $where)
	{
		return $this->findBy($where)->applyLimit(1)->fetch();
	}

	protected function getPersistenceHelper()
	{
		$h = new DibiPersistenceHelper;
		$h->connection = $this->getConnection();
		$h->conventional = $this->getConventional();
		$h->table = $this->getTableName();
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

	public function persist(Entity $entity)
	{
		$this->begin();
		return $this->getPersistenceHelper()->persist($entity);
	}


	public function delete($entity)
	{
		$entityId = $entity instanceof Entity ? $entity->id : $entity;

		$result = false;

		if ($entityId)
		{
			$this->begin();
			$result = (bool) $this->getConnection()->delete($this->getTableName())->where('[id] = %i', $entityId)->execute();
		}
		if ($entity instanceof Entity)
		{
			Entity::internalValues($entity, array('id' => NULL));
		}
		// todo clean Repository::$entities[$entityId]

		return $result;
	}

	public function flush()
	{
		$this->getConnection()->commit();
	}

	protected function dataSource()
	{
		$connection = $this->getConnection();
		$args = func_get_args();
		if (!$connection->isConnected())
			$connection->sql(''); // protoze nema public metodu DibiConnection::connect()
		$translator = new DibiTranslator($connection->driver);
		return new DibiModelDataSource($translator->translate($args), $connection, $this->repository);
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

}
// todo refactor
class DibiPersistenceHelper extends Object
{
	public $table;
	public $connection;
	public $conventional;

	public $witchParams = NULL;
	public $witchParamsNot = NULL;

	public function persist(Entity $entity)
	{
		$values = Entity::internalValues($entity);
		$manyToManyValues = array();
		$fk = Entity::getFk(get_class($entity));

		foreach ($values as $key => $value)
		{
			if (($this->witchParams !== NULL AND !in_array($key, $this->witchParams)) OR ($this->witchParamsNot !== NULL AND in_array($key, $this->witchParamsNot)))
			{
				unset($values[$key]);
				continue;
			}

			if (isset($fk[$key]) AND $value instanceof Entity)
			{
				Model::getRepository($fk[$key])->persist($value, false);
				$values[$key] = $value->id;
			}
			else if (is_array($value))
			{
				$values[$key] = serialize($value); // todo zkontrolovat jestli je jednodimenzni a neobrahuje zadne nesmysly
			}
			else if ($value instanceof ManyToMany)
			{
				$manyToManyValues[] = $value;;
				unset($values[$key]);
			}
			else if ($value !== NULL AND !($value instanceof DateTime) AND !is_scalar($value))
			{
				throw new InvalidStateException("Neumim ulozit `".get_class($entity)."::$$key` " . (is_object($value) ? get_class($value) : gettype($value)));
			}
		}

		$values = $this->conventional->formatEntityToStorage($values);
		$table = $this->table;
		if (isset($entity->id) AND $this->connection->fetch('SELECT [id] FROM %n WHERE [id] = %i', $table, $entity->id))
		{
			$id = $entity->id;
			$this->connection->update($table, $values)->where('[id] = %i', $id)->execute();
		}
		else
		{
			$this->connection->insert($table, $values)->execute();
			try {
				$id = $this->connection->getInsertId();
			} catch (DibiException $e) {
				if (!isset($entity->id)) throw $e;
				$id = $entity->id;
			}
			Entity::internalValues($entity, array('id' => $id));
		}

		foreach ($manyToManyValues as $manyToMany)
		{
			$manyToMany->persist(false);
		}

		return $id;
	}

}

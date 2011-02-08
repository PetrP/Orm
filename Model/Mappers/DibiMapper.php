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
		$translator = new DibiTranslator($connection->driver);
		$class = $this->getCollectionClass();
		return new $class($translator->translate($args), $connection, $this->repository);
	}

	protected function createCollectionClass()
	{
		return 'DibiModelDataSource';
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

<?php

/**
* @property-read DibiConnection $connection
*/
class SimpleSqlMapper extends Mapper
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
		return new SqlConventional;
	}
	
	
	public function findAll()
	{
		return $this->dataSource($this->getTableName());
	}
	
	protected function findBy(array $where)
	{
		$all = $this->findAll();
		$where = $this->getConventional()->format($where, $this->repository->getEntityName());
		// todo instanceof IModelDataSource
		foreach ($where as $key => $value)
		{
			$all->where('%n = %s', $key, $value instanceof Entity ? $value->id : $value);
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
		$h->connection = $this->connection;
		$h->conventional = $this->getConventional();
		$h->table = $this->getTableName();
		return $h;
	}
	
	public function persist(Entity $entity, $useTransaction = true)
	{
		return $this->getPersistenceHelper()->persist($entity, $useTransaction);
	}
	
	protected function dataSource()
	{
		$connection = $this->getConnection();
		$args = func_get_args();
		if (!$connection->isConnected())
			$connection->sql(''); // protoze nema public metodu DibiConnection::connect()
		$translator = new DibiTranslator($connection->driver);
		return new ModelDataSource($translator->translate($args), $connection, $this->repository);
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
	
	public function persist(Entity $entity, $useTransaction = true)
	{
		$values = Entity::getPrivateValues($entity);
		$fk = Entity::getFk(get_class($entity));
		if ($useTransaction)
		{
			$this->connection->begin();
		}
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
			else if ($value !== NULL AND !is_scalar($value))
			{
				throw new InvalidStateException("Neumim ulozit `".get_class($entity)."::$$key` " . gettype($value));
			}
		}
		
		$values = $this->conventional->format($values, get_class($entity));
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
			Entity::setPrivateValues($entity, array('id' => $id));
		}
		if ($useTransaction)
		{
			$this->connection->commit();
		}
		return $id;
	}
	
}

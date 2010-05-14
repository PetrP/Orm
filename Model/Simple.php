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
		return $this->dataSource($this->repository->getRepositoryName());
	}
	
	protected function findBy(array $where)
	{
		$all = $this->findAll();
		$where = $this->getConventional()->format($where, $this->repository->getEntityName());
		// todo instanceof DibiDataSource
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
	
	public function persist(Entity $e, $useTransaction = true)
	{
		$values = Entity::getPrivateValues($e);
		$fk = Entity::getFk(get_class($e));
		if ($useTransaction)
		{
			$this->connection->begin();
		}
		foreach ($values as $key => $value)
		{
			if (isset($fk[$key]) AND $value instanceof Entity)
			{
				Model::getRepository($fk[$key])->persist($value, false);
				$values[$key] = $value->id;
			}
			else if ($value !== NULL AND !is_scalar($value))
			{
				throw new InvalidStateException("Neumim ulozit `".get_class($e)."::$$key` " . gettype($value));
			}
		}
		
		$values = $this->getConventional()->format($values, get_class($e));
		$table = $this->repository->getRepositoryName();
		if (isset($e->id))
		{
			$id = $e->id;
			$this->connection->update($table, $values)->where('[id] = %i', $id)->execute();
		}
		else
		{
			$id = $this->connection->insert($table, $values)->execute(dibi::IDENTIFIER);
			Entity::setPrivateValues($e, array('id' => $id));
		}
		if ($useTransaction)
		{
			$this->connection->commit();
		}
		return $id;
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
	
}

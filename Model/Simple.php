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
		$where = $this->getConventional()->unformat($where, $this->repository->getEntityName());
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
	
	public function persist(Entity $e)
	{
		$values = Entity::getPrivateValues($e);
		$table = $this->repository->getRepositoryName();
		if (isset($e->id))
		{
			$id = $e->id;
			$this->connection->update($table, $values)->where('[id] = %i', $id)->execute();
			return $id;
		}
		else
		{
			$id = $this->connection->insert($table, $values)->execute(dibi::IDENTIFIER);
			Entity::setPrivateValues($e, array('id' => $id));
			return $id;
		}
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

<?php

/**
* @property-read DibiConnection $connection
*/
class SimpleSqlMapper extends Mapper
{
	
	public function getConnection()
	{
		return dibi::getConnection();
	}
	
	
	public function findAll()
	{
		return $this->dataSource('SELECT * FROM %n' , $this->repository->getRepositoryName());
	}
	
	protected function findBy(array $where)
	{
		$all = $this->findAll();
		// todo instanceof DibiDataSource
		foreach ($where as $key => $value)
		{
			if ($value instanceof Entity)
			{
				$all->where('%n = %s', $key . '_id', $value->id); // todo convencional
			}
			else
			{
				$all->where('%n = %s', $key, $value);
			}
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

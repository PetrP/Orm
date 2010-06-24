<?php

require_once dirname(__FILE__) . '/Mapper.php';

require_once dirname(__FILE__) . '/DataSource/ArrayDataSource.php';

abstract class ArrayMapper extends Mapper
{
	private $data;

	public function findAll()
	{
		return new ArrayDataSource(array_values($this->getData()));
	}
	protected function getData()
	{
		if (!isset($this->data))
		{
			$this->data = array();
			$repository = $this->repository;
			foreach ($this->loadData() as $id => $row)
			{
				if ($row !== NULL)
				{
					$this->data[$id] = $repository->createEntity($row);
				}
			}
		}
		return $this->data;
	}
	protected function loadData()
	{
		throw new NotImplementedException();
	}
	protected function saveData(array $data)
	{
		throw new NotImplementedException();
	}

	protected function findBy(array $where)
	{
		$all = $this->getData();
		$result = array();
		foreach ($where as $key => $value)
		{
			foreach ($all as $entity)
			{
				$value = $value instanceof Entity ? $value->id : $value;
				$eValue = $entity[$key];
				$eValue = $eValue instanceof Entity ? $eValue->id : $eValue;
				if ($eValue == $value)
				{
					$result[] = $entity;
				}
			}
		}
		return new ArrayDataSource($result);
	}
	protected function getBy(array $where)
	{
		return $this->findBy($where)->applyLimit(1)->fetch();
	}

	public function getById($id)
	{
		if (!$id) return NULL;
		$data = $this->getData();
		return isset($data[$id]) ? $data[$id] : NULL;
	}

	public function persist(Entity $entity, $beAtomic = true)
	{

		Environment::enterCriticalSection(get_class($this));

		$values = Entity::internalValues($entity);
		$fk = Entity::getFk(get_class($entity));
		foreach ($values as $key => $value)
		{
			if (isset($fk[$key]) AND $value instanceof Entity)
			{
				Model::getRepository($fk[$key])->persist($value, false);
				$values[$key] = $value->id;
			}
			else if ($value !== NULL AND !is_scalar($value) AND !is_array($value))
			{
				throw new InvalidStateException("Neumim ulozit `".get_class($entity)."::$$key` " . gettype($value));
			}
		}
		$originData = $this->loadData();
		if (isset($entity->id) AND isset($originData[$entity->id]))
		{
			$id = $entity->id;
			$originData[$entity->id] = $values;
		}
		else
		{
			$id = $originData ? max(array_keys($originData)) + 1 : 1;
			Entity::internalValues($entity, array('id' => $id));
			$values['id'] = $id;
			$originData[$id] = $values;
		}
		$this->data[$id] = $entity;

		$this->saveData($originData);

		Environment::leaveCriticalSection(get_class($this));

		return $id;
	}

	public function delete($entity, $beAtomic = true)
	{
		$entityId = $entity instanceof Entity ? $entity->id : $entity;

		$result = false;

		if ($entityId)
		{
			Environment::enterCriticalSection(get_class($this));

			$originData = $this->loadData();
			if (isset($originData[$entityId]))
			{
				$originData[$entityId] = NULL;
			}

			if (isset($this->data[$entityId]))
			{
				unset($this->data[$entityId]);
			}

			$this->saveData($originData);

			Environment::leaveCriticalSection(get_class($this));

		}
		if ($entity instanceof Entity)
		{
			Entity::internalValues($entity, array('id' => NULL));
		}
		// todo clean Repository::$entities[$entityId]

		return $result;
	}
}


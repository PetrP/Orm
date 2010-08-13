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
				$this->data[$id] = $row ? $repository->createEntity($row) : NULL;
			}
		}
		return array_filter($this->data);
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
		foreach ($all as $entity)
		{
			$equal = false;
			foreach ($where as $key => $value)
			{
				$eValue = $entity[$key];
				$eValue = $eValue instanceof Entity ? $eValue->id : $eValue;
				$value = $value instanceof Entity ? $value->id : $value;

				if ($eValue == $value OR (is_array($value) AND in_array($eValue, $value)))
				{
					$equal = true;
				}
				else
				{
					$equal = false;
					break;
				}
			}
			if ($equal)
			{
				$result[] = $entity;
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

	public function persist(Entity $entity)
	{
		$this->begin();

		$manyToManyValues = array();
		$fk = Entity::getFk(get_class($entity));
		foreach (Entity::internalValues($entity) as $key => $value)
		{
			if (isset($fk[$key]) AND $value instanceof Entity)
			{
				Model::getRepository($fk[$key])->persist($value, false);
			}
			else if ($value instanceof ManyToMany)
			{
				$manyToManyValues[] = $value;;
			}
		}

		if (isset($entity->id) AND isset($this->data[$entity->id]))
		{
			$id = $entity->id;
		}
		else
		{
			Environment::enterCriticalSection(get_class($this));
			$originData = $this->loadData();
			$id = $this->data ? max(array_keys($originData)) + 1 : 1;
			$originData[$id] = NULL;
			$this->saveData($originData);
			Environment::leaveCriticalSection(get_class($this));
			Entity::internalValues($entity, array('id' => $id));
		}
		$this->data[$id] = $entity;

		foreach ($manyToManyValues as $manyToMany)
		{
			$manyToMany->persist(false);
		}

		return $id;
	}

	public function delete($entity)
	{
		$entityId = $entity instanceof Entity ? $entity->id : $entity;

		$result = false;

		$this->begin();

		if ($entityId AND isset($this->data[$entityId]))
		{
			$this->data[$entityId] = NULL;
		}
		if ($entity instanceof Entity)
		{
			Entity::internalValues($entity, array('id' => NULL));
		}
		// todo clean Repository::$entities[$entityId]

		return $result;
	}

	public function begin()
	{
		$this->getData();
	}

	final public function rollback()
	{
		$this->data = NULL;
		// todo zmeny zustanou v Repository::$entities
	}

	public function flush()
	{
		if (!$this->data) return;

		Environment::enterCriticalSection(get_class($this));
		$originData = $this->loadData();

		foreach ($this->data as $id => $entity)
		{
			if ($entity)
			{
				$values = Entity::internalValues($entity);
				$fk = Entity::getFk(get_class($entity));
				foreach ($values as $key => $value)
				{
					if (isset($fk[$key]) AND $value instanceof Entity)
					{
						$values[$key] = $value->id;
					}
					else if ($value instanceof ManyToMany)
					{
						unset($values[$key]);
					}
					else if ($value instanceof DateTime)
					{
						$values[$key] = $value->format('c');
					}
					else if ($value !== NULL AND !is_scalar($value) AND !is_array($value))
					{
						throw new InvalidStateException("Neumim ulozit `".get_class($entity)."::$$key` " . (is_object($value) ? get_class($value) : gettype($value)));
					}
				}

				$originData[$id] = $values;
			}
			else
			{
				$originData[$id] = NULL;
			}
		}

		$this->saveData($originData);
		Environment::leaveCriticalSection(get_class($this));
	}
}

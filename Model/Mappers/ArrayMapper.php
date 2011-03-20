<?php

require_once dirname(__FILE__) . '/Mapper.php';

require_once dirname(__FILE__) . '/Collection/ArrayCollection.php';

abstract class ArrayMapper extends Mapper
{
	private $data;

	public function findAll()
	{
		$class = $this->getCollectionClass();
		return new $class(array_values($this->getData()));
	}

	protected function createCollectionClass()
	{
		return 'ArrayCollection';
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

	public function getById($id)
	{
		if (!$id) return NULL;
		$data = $this->getData();
		return isset($data[$id]) ? $data[$id] : NULL;
	}

	public function persist(IEntity $entity)
	{
		$this->begin();

		if (isset($entity->id) AND isset($this->data[$entity->id]))
		{
			$id = $entity->id;
		}
		else
		{
			if (method_exists('Tools', 'enterCriticalSection')) Tools::enterCriticalSection();
			else Environment::enterCriticalSection(get_class($this));
			$originData = $this->loadData();
			$id = $originData ? max(array_keys($originData)) + 1 : 1;
			$originData[$id] = NULL;
			$this->saveData($originData);
			if (method_exists('Tools', 'leaveCriticalSection')) Tools::leaveCriticalSection();
			else Environment::leaveCriticalSection(get_class($this));
		}
		$this->data[$id] = $entity;

		return $id;
	}

	public function remove(IEntity $entity)
	{
		// todo pri vymazavani odstanit i vazby v IRelationship
		$this->begin();
		$this->data[$entity->id] = NULL;
		return true;
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

		if (method_exists('Tools', 'enterCriticalSection')) Tools::enterCriticalSection();
		else Environment::enterCriticalSection(get_class($this));
		$originData = $this->loadData();

		foreach ($this->data as $id => $entity)
		{
			if ($entity)
			{
				$values = $entity->toArray();
				foreach ($values as $key => $value)
				{
					if ($value instanceof IEntityInjection)
					{
						$values[$key] = $value = $value->getInjectedValue();
					}

					if ($value instanceof IEntity)
					{
						$values[$key] = $value->id;
					}
					else if ($value instanceof IRelationship)
					{
						unset($values[$key]);
					}
					else if ($value instanceof DateTime)
					{
						$values[$key] = $value->format('c');
					}
					else if (is_object($value) AND method_exists($value, '__toString'))
					{
						$values[$key] = $value->__toString();
					}
					else if ($value !== NULL AND !is_scalar($value) AND !is_array($value) AND !($value instanceof ArrayObject AND get_class($value) == 'ArrayObject'))
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
		if (method_exists('Tools', 'leaveCriticalSection')) Tools::leaveCriticalSection();
		else Environment::leaveCriticalSection(get_class($this));
	}
}

<?php

namespace Orm;

use Nette\NotImplementedException;
use Nette\InvalidStateException;
use DateTime;
use ArrayObject;

require_once dirname(__FILE__) . '/Mapper.php';
require_once dirname(__FILE__) . '/Collection/ArrayCollection.php';

abstract class ArrayMapper extends Mapper
{
	private $data;

	private static $lock;

	public function findAll()
	{
		$class = $this->getCollectionClass();
		return new $class(array_values($this->getData()));
	}

	protected function createCollectionClass()
	{
		return 'Orm\ArrayCollection';
	}

	public function createManyToManyMapper($firstParam, IRepository $repository, $secondParam)
	{
		return new ArrayManyToManyMapper;
	}

	protected function getData()
	{
		if (!isset($this->data))
		{
			$this->data = array();
			$repository = $this->getRepository();
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
			$this->lock();
			$originData = $this->loadData();
			$id = $originData ? max(array_keys($originData)) + 1 : 1;
			$originData[$id] = NULL;
			$this->saveData($originData);
			$this->unlock();
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

	protected function begin()
	{
		$this->getData();
	}

	public function rollback()
	{
		$this->data = NULL;
		// todo zmeny zustanou v Repository::$entities
	}

	public function flush()
	{
		if (!$this->data) return;

		$this->lock();
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
		$this->unlock();
	}

	/**
	 * Enters the critical section, other threads are locked out.
	 * @author David Grudl
	 */
	protected function lock()
	{
		if (self::$lock)
		{
			throw new InvalidStateException('Critical section has already been entered.');
		}
		// locking on Windows causes that a file seems to be empty
		$handle = substr(PHP_OS, 0, 3) === 'WIN'
			? @fopen(dirname(__FILE__) . '/ArrayMapper.lockfile', 'w')
			: @fopen(__FILE__, 'r'); // @ - file may not already exist

		if (!$handle)
		{
			throw new InvalidStateException("Unable initialize critical section.");
		}
		flock(self::$lock = $handle, LOCK_EX);
	}

	/**
	 * Leaves the critical section, other threads can now enter it.
	 * @author David Grudl
	 */
	protected function unlock()
	{
		if (!self::$lock)
		{
			throw new InvalidStateException('Critical section has not been initialized.');
		}
		flock(self::$lock, LOCK_UN);
		fclose(self::$lock);
		self::$lock = NULL;
	}

}

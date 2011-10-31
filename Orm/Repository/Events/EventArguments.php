<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * @property-read int $type
 * @property-read NULL|IEntity $entity
 * @property-read NULL|string $operation update|insert
 * @property-read IRepository $repository
 * @author Petr Procházka
 * @package Orm
 * @subpackage Repository\Events
 */
class EventArguments extends Object
{
	/** @var array */
	public $data;

	/** @var scalar */
	public $id;

	/** @var array */
	public $params;

	/** @var array */
	public $values;

	/** @var NULL|string update|insert */
	private $operation;

	/** @var int event type */
	private $type;

	/** @var array (key => true) allowed keys for this event */
	private $keys;

	/** @var NULL|IEntity */
	private $entity;

	/** @var IRepository */
	private $repository;

	/** @var array allowed keys for events */
	private static $typeKeys = array(
		Events::HYDRATE_BEFORE => array('entity' => true, 'data' => true),
		Events::HYDRATE_AFTER => array('entity' => true, 'data' => true),
		Events::ATTACH => array('entity' => true),
		Events::PERSIST_BEFORE => array('entity' => true),
		Events::PERSIST_BEFORE_UPDATE => array('entity' => true),
		Events::PERSIST_BEFORE_INSERT => array('entity' => true),
		Events::PERSIST => array('entity' => true, 'id' => true),
		Events::PERSIST_AFTER_UPDATE => array('entity' => true),
		Events::PERSIST_AFTER_INSERT => array('entity' => true),
		Events::PERSIST_AFTER => array('entity' => true),
		Events::REMOVE_BEFORE => array('entity' => true),
		Events::REMOVE_AFTER => array('entity' => true),
		Events::FLUSH_BEFORE => array(),
		Events::FLUSH_AFTER => array(),
		Events::CLEAN_BEFORE => array(),
		Events::CLEAN_AFTER => array(),
		Events::SERIALIZE_BEFORE => array('entity' => true, 'params' => true, 'values' => true, 'operation' => true),
		Events::SERIALIZE_AFTER => array('entity' => true, 'values' => true, 'operation' => true),
		Events::SERIALIZE_CONVENTIONAL => array('entity' => true, 'values' => true, 'operation' => true),
	);

	/**
	 * @param int event
	 * @param IRepository
	 * @param IEntity|NULL
	 * @param array
	 */
	public function __construct($type, IRepository $repository, IEntity $entity = NULL, array $arguments = array())
	{
		if (!isset(self::$typeKeys[$type]))
		{
			throw new InvalidArgumentException(array($this, '$type', 'valid event type', $type));
		}
		$this->keys = self::$typeKeys[$type];
		$this->type = $type;
		$this->repository = $repository;
		if (isset($this->keys['entity']))
		{
			$this->entity = $entity;
			if (!$entity)
			{
				throw new InvalidArgumentException(array($this, '$entity', 'instance of Orm\IEntity', $entity));
			}
		}
		if (isset($this->keys['id']))
		{
			$this->id = isset($arguments['id']) ? $arguments['id'] : NULL;
		}
		else
		{
			unset($this->id);
		}
		if (isset($this->keys['data']))
		{
			$this->data = isset($arguments['data']) ? $arguments['data'] : NULL;
		}
		else
		{
			unset($this->data);
		}
		if (isset($this->keys['operation']))
		{
			$this->operation = isset($arguments['operation']) ? $arguments['operation'] : NULL;
			if ($this->operation !== 'update' AND $this->operation !== 'insert')
			{
				throw new InvalidArgumentException(array($this, '$operation', 'update|insert', $this->operation));
			}
		}
		if (isset($this->keys['params']))
		{
			$this->params = isset($arguments['params']) ? $arguments['params'] : NULL;
		}
		else
		{
			unset($this->params);
		}
		if (isset($this->keys['values']))
		{
			$this->values = isset($arguments['values']) ? $arguments['values'] : NULL;
		}
		else
		{
			unset($this->values);
		}
		$this->check();
	}

	/** @return int event type */
	public function getType()
	{
		return $this->type;
	}

	/** @return NULL|IEntity */
	public function getEntity()
	{
		if (!$this->entity)
		{
			throw new MemberAccessException("Cannot read an undeclared property Orm\\EventArguments::\$entity.");
		}
		return $this->entity;
	}

	/** @return NULL|string update|insert */
	public function getOperation()
	{
		if (!$this->operation)
		{
			throw new MemberAccessException("Cannot read an undeclared property Orm\\EventArguments::\$operation.");
		}
		return $this->operation;
	}

	/** @return IRepository */
	public function getRepository()
	{
		return $this->repository;
	}

	/**
	 * Check if all writable params are valid.
	 * @throws InvalidArgumentException
	 */
	public function check()
	{
		if (isset($this->keys['id']))
		{
			if (!is_scalar($this->id))
			{
				throw new InvalidArgumentException(array($this, '$id', 'scalar', $this->id));
			}
		}
		if (isset($this->keys['data']))
		{
			if (!is_array($this->data))
			{
				throw new InvalidArgumentException(array($this, '$data', 'array', $this->data));
			}
		}
		if (isset($this->keys['params']))
		{
			if (!is_array($this->params))
			{
				throw new InvalidArgumentException(array($this, '$params', 'array', $this->params));
			}
		}
		if (isset($this->keys['values']))
		{
			if (!is_array($this->values))
			{
				throw new InvalidArgumentException(array($this, '$values', 'array', $this->values));
			}
		}
	}

	/** @return array */
	public function getArguments()
	{
		$keys = $this->keys;
		unset($keys['entity']);
		$arguments = array();
		foreach ($keys as $key => $tmp)
		{
			$arguments[$key] = $this->{$key};
		}
		return $arguments;
	}
}

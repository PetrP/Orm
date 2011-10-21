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
		Events::LOAD_BEFORE => array('entity' => true, 'data' => true),
		Events::LOAD_AFTER => array('entity' => true, 'data' => true),
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
		$this->entity = $entity;
		if (isset($this->keys['entity']) AND !$entity)
		{
			throw new InvalidArgumentException(array($this, '$entity', 'instance of Orm\IEntity', $entity));
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
		return $this->entity;
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
	}
}

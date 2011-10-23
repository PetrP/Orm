<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use ArrayObject;
use DateTime;
use DibiException;
use DibiConnection;

/**
 * Helps customize persist.
 *
 * <code>
 * // DibiMapper
 * public function persist(IEntity $entity)
 * {
 * 	$h = $this->getPersistenceHelper();
 * 	...
 * 	return $h->persist($entity);
 * }
 *
 * // save date as unix time
 * $h->params['date'] = function (DateTime $date, IEntity $entity) {
 * 	return $date->format('U');
 * };
 *
 * // ignore same param
 * $h->params['foo'] = false;
 *
 * </code>
 * @author Petr Procházka
 * @package Orm
 * @subpackage Mappers\Helpers
 */
class DibiPersistenceHelper extends Object
{

	/** @var string */
	public $table;

	/** @var string */
	public $primaryKey;

	/** @var array of bool|callback */
	public $params = array();

	/** @var array|NULL */
	public $whichParams = NULL; // deprecated

	/** @var array|NULL */
	public $whichParamsNot = NULL; // deprecated

	/** @var DibiConnection */
	private $connection;

	/** @var IDatabaseConventional */
	private $conventional;

	/** @var Events */
	private $events;

	/**
	 * @param DibiConnection
	 * @param IDatabaseConventional
	 * @param string
	 * @param Events
	 */
	public function __construct(DibiConnection $connection, IDatabaseConventional $conventional, $table, Events $events)
	{
		$this->connection = $connection;
		$this->conventional = $conventional;
		$this->table = $table;
		$this->events = $events;
		$this->primaryKey = $conventional->getPrimaryKey();
	}

	/**
	 * @param IEntity
	 * @param scalar|NULL id
	 * @return scalar id
	 */
	public function persist(IEntity $entity, $id = NULL)
	{
		$hasEntry = $this->hasEntry($entity);
		$values = $this->toArray($entity, $id, $hasEntry ? 'update' : 'insert');

		if ($hasEntry)
		{
			$id = $entity->id;
			$this->update($values, $id);
		}
		else
		{
			$id = $this->insert($values);
		}
		return $id;
	}

	/** @return DibiConnection */
	public function getConnection()
	{
		return $this->connection;
	}

	/** @return IDatabaseConventional */
	public function getConventional()
	{
		return $this->conventional;
	}

	/** @return Events */
	public function getEvents()
	{
		return $this->events;
	}

	/**
	 * @param IEntity
	 * @param scalar|NULL id
	 * @param string update|insert
	 * @return array
	 */
	protected function toArray(IEntity $entity, $id, $operation)
	{
		$values = $entity->toArray();
		if ($id !== NULL) $values['id'] = $id;

		$params = array('id' => isset($values['id'])) + (array) $this->params;
		$params += array_fill_keys(array_keys($values), true);

		if ($this->whichParams !== NULL) // bc, deprecated
		{
			$params = array('id' => $params['id']) + array_fill_keys($this->whichParams, true);
		}
		if ($this->whichParamsNot !== NULL) // bc, deprecated
		{
			$tmp = array_fill_keys($this->whichParamsNot, false);
			unset($tmp['id']);
			$params = $tmp + $params;
		}

		$arguments = array('params' => $params, 'values' => $values, 'operation' => $operation);
		$this->events->fireEvent(Events::SERIALIZE_BEFORE, $entity, $arguments);
		$params = $arguments['params'];
		$values = $arguments['values'];

		$result = array();
		foreach ($params as $key => $do)
		{
			if (array_key_exists($key, $values))
			{
				$value = $values[$key];
			}
			else
			{
				// pokusi se precist, muze existovat getter, jinak vyhodi exception
				$value = $entity->{$key};
			}
			if ($do === false)
			{
				continue;
			}
			if ($do !== true)
			{
				$value = Callback::create($do)->invoke($value, $entity);
			}

			$result[$key] = $this->scalarizeValue($value, $key, $entity);
			if ($value instanceof IRelationship AND $result[$key] === NULL)
			{
				unset($result[$key]);
			}
		}

		$arguments = array('values' => $result, 'operation' => $operation);
		$this->events->fireEvent(Events::SERIALIZE_AFTER, $entity, $arguments);
		$result = $arguments['values'];

		$result = $this->conventional->formatEntityToStorage($result);
		$primaryKey = $this->conventional->getPrimaryKey();
		if ($primaryKey !== $this->primaryKey AND array_key_exists($primaryKey, $result))
		{
			$id = $result[$primaryKey];
			unset($result[$primaryKey]);
			$result = array($this->primaryKey => $id) + $result;
		}

		$arguments = array('values' => $result, 'operation' => $operation);
		$this->events->fireEvent(Events::SERIALIZE_CONVENTIONAL, $entity, $arguments);
		$result = $arguments['values'];

		return $result;
	}

	/**
	 * @param mixed
	 * @param string
	 * @param IEntity
	 * @return scalar|NULL|DateTime
	 * @throws MapperPersistenceException
	 */
	protected function scalarizeValue($value, $key, IEntity $entity)
	{
		if ($value instanceof IEntityInjection)
		{
			$value = $value->getInjectedValue();
		}

		if ($value instanceof IEntity)
		{
			$value = $value->id;
		}
		else if (is_array($value) OR ($value instanceof ArrayObject AND get_class($value) == 'ArrayObject'))
		{
			$value = serialize($value); // todo zkontrolovat jestli je jednodimenzni a neobrahuje zadne nesmysly
		}
		else if (is_object($value) AND method_exists($value, '__toString'))
		{
			$value = $value->__toString();
		}
		else if ($value !== NULL AND !($value instanceof DateTime) AND !is_scalar($value))
		{
			$mapper = $entity->getRepository(false) ? $entity->getRepository()->getMapper() : $this;
			throw new MapperPersistenceException(array($mapper, $entity, $key, $value));
		}

		return $value;
	}

	/**
	 * @param IEntity
	 * @return bool
	 */
	protected function hasEntry(IEntity $entity)
	{
		return isset($entity->id) AND $this->connection->fetch('SELECT %n FROM %n WHERE %n = %s', $this->primaryKey, $this->table, $this->primaryKey, $entity->id);
	}

	/**
	 * @param array
	 * @param scalar id
	 * @return void
	 */
	protected function update(array $values, $id)
	{
		$this->connection->update($this->table, $values)->where('%n = %s', $this->primaryKey, $id)->execute();
	}

	/**
	 * @param array
	 * @return scalar id
	 */
	protected function insert(array $values)
	{
		$this->connection->insert($this->table, $values)->execute();
		try {
			$id = $this->connection->getInsertId();
		} catch (DibiException $e) {
			if (isset($values[$this->primaryKey]))
			{
				$id = $values[$this->primaryKey];
			}
			else
			{
				throw $e;
			}
		}
		return $id;
	}

	/** @deprecated */
	final public function getWitchParams()
	{
		return $this->whichParams;
	}

	/** @deprecated */
	final public function setWitchParams($p)
	{
		$this->whichParams = $p;
	}

	/** @deprecated */
	final public function getWitchParamsNot()
	{
		return $this->whichParamsNot;
	}

	/** @deprecated */
	final public function setWitchParamsNot($p)
	{
		$this->whichParamsNot = $p;
	}

	/** @deprecated */
	final public function setConnection()
	{
		throw new DeprecatedException(array(__CLASS__, '$connection setter', 'constructor'));
	}

	/** @deprecated */
	final public function setConventional()
	{
		throw new DeprecatedException(array(__CLASS__, '$conventional setter', 'constructor'));
	}

	/** @deprecated */
	final public function getMapper()
	{
		throw new DeprecatedException(array(__CLASS__, '$mapper'));
	}

	/** @deprecated */
	final public function setMapper()
	{
		throw new DeprecatedException(array(__CLASS__, '$mapper'));
	}

}

<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use Nette\Object;
use ArrayObject;
use DateTime;
use Nette\InvalidStateException;
use Nette\DeprecatedException;
use DibiException;
use DibiConnection;

/**
 * Helps customize persist.
 *
 * <pre>
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
 * </pre>
 */
class DibiPersistenceHelper extends Object
{

	/** @var string */
	public $table;

	/** @var array of bool|callback */
	public $params = array();

	/** @var array|NULL */
	public $witchParams = NULL; // deprecated

	/** @var array|NULL */
	public $witchParamsNot = NULL; // deprecated

	/** @var DibiConnection */
	private $connection;

	/** @var IDatabaseConventional */
	private $conventional;

	/**
	 * @param DibiConnection
	 * @param IDatabaseConventional
	 */
	public function __construct(DibiConnection $connection, IDatabaseConventional $conventional, $table)
	{
		$this->connection = $connection;
		$this->conventional = $conventional;
		$this->table = $table;
	}

	/**
	 * @param IEntity
	 * @param scalar|NULL id
	 * @return scalar id
	 */
	public function persist(IEntity $entity, $id = NULL)
	{
		$values = $this->toArray($entity, $id);

		if ($this->hasEntry($entity))
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

	/**
	 * @param IEntity
	 * @param scalar|NULL id
	 * @return array
	 */
	protected function toArray(IEntity $entity, $id)
	{
		$values = $entity->toArray();
		if ($id !== NULL) $values['id'] = $id; // todo je to potreba?

		$params = array('id' => isset($values['id'])) + (array) $this->params;
		$params += array_fill_keys(array_keys($values), true);

		if ($this->witchParams !== NULL) // bc, deprecated
		{
			$params = array('id' => $params['id']) + array_fill_keys($this->witchParams, true);
			// todo witchParams mozna zrusit
		}
		if ($this->witchParamsNot !== NULL) // bc, deprecated
		{
			$tmp = array_fill_keys($this->witchParamsNot, false);
			unset($tmp['id']);
			$params = $tmp + $params;
		}

		$result = array();
		foreach ($params as $key => $do)
		{
			if ($do === false)
			{
				continue;
			}
			if (array_key_exists($key, $values))
			{
				$value = $values[$key];
			}
			else
			{
				// pokusi se precist, muze existovat getter, jinak vyhodi exception
				$value = $entity->{$key};
			}
			if ($do !== true)
			{
				$value = callback($do)->invoke($value, $entity);
			}

			$result[$key] = $this->scalarizeValue($value, $key, $entity);
		}

		return $this->conventional->formatEntityToStorage($result);
	}

	/**
	 * @param mixed
	 * @param string
	 * @param IEntity
	 * @return scalar|NULL|DateTime
	 * @throws InvalidStateException
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
			throw new InvalidStateException("Neumim ulozit `".get_class($entity)."::$$key` " . (is_object($value) ? get_class($value) : gettype($value)));
		}

		return $value;
	}

	/**
	 * @param IEntity
	 * @return bool
	 */
	protected function hasEntry(IEntity $entity)
	{
		return isset($entity->id) AND $this->connection->fetch('SELECT [id] FROM %n WHERE [id] = %s', $this->table, $entity->id);
	}

	/**
	 * @param array
	 * @param scalar id
	 * @return void
	 */
	protected function update(array $values, $id)
	{
		$this->connection->update($this->table, $values)->where('[id] = %s', $id)->execute();
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
			if (isset($values['id']))
			{
				$id = $values['id'];
			}
			else
			{
				throw $e;
			}
		}
		return $id;
	}

	/** @deprecated */
	final public function setConnection() { throw new DeprecatedException(__CLASS__ . '::$connection setter is depreacted; use constructor instead'); }
	/** @deprecated */
	final public function setConventional() { throw new DeprecatedException(__CLASS__ . '::$conventional setter is depreacted; use constructor instead'); }
	/** @deprecated */
	final public function getMapper() { throw new DeprecatedException(__CLASS__ . '::$mapper is depreacted'); }
	/** @deprecated */
	final public function setMapper() { throw new DeprecatedException(__CLASS__ . '::$mapper is depreacted'); }

}

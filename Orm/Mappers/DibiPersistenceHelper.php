<?php

namespace Orm;

use Nette\Object;
use ArrayObject;
use DateTime;
use Nette\InvalidStateException;
use DibiException;

// todo refactor constructor
class DibiPersistenceHelper extends Object
{

	/** @var string */
	public $table;

	/** @var DibiConnection */
	public $connection;

	/** @var IConventional */
	public $conventional;

	/** @var DibiMapper */
	public $mapper;

	/** @var array|NULL */
	public $witchParams = NULL;

	/** @var array|NULL */
	public $witchParamsNot = NULL;

	/**
	 * @param IEntity
	 * @param scalar|NULL id
	 * @return scalar id
	 * @todo refactor
	 */
	public function persist(IEntity $entity, $id = NULL)
	{
		$values = $entity->toArray();
		if ($id !== NULL) $values['id'] = $id;

		foreach ($values as $key => $value)
		{
			if ($key !== 'id' AND (($this->witchParams !== NULL AND !in_array($key, $this->witchParams)) OR ($this->witchParamsNot !== NULL AND in_array($key, $this->witchParamsNot))))
			{
				unset($values[$key]);
				continue;
			}
			if ($value instanceof IEntityInjection)
			{
				$values[$key] = $value = $value->getInjectedValue();
			}

			if ($value instanceof IEntity)
			{
				$values[$key] = $value->id;
			}
			else if (is_array($value) OR ($value instanceof ArrayObject AND get_class($value) == 'ArrayObject'))
			{
				$values[$key] = serialize($value); // todo zkontrolovat jestli je jednodimenzni a neobrahuje zadne nesmysly
			}
			else if (is_object($value) AND method_exists($value, '__toString'))
			{
				$values[$key] = $value->__toString();
			}
			else if ($value !== NULL AND !($value instanceof DateTime) AND !is_scalar($value))
			{
				throw new InvalidStateException("Neumim ulozit `".get_class($entity)."::$$key` " . (is_object($value) ? get_class($value) : gettype($value)));
			}
		}

		$values = $this->conventional->formatEntityToStorage($values);
		$table = $this->table;

		if (method_exists($this->connection->driver, 'getReflector'))
		{
			$columns = $this->connection->driver->getReflector()->getColumns($table);
		}
		else
		{
			$columns = $this->connection->driver->getColumns($table);
		}
		// todo inline cache

		$tmp = array();
		foreach ($columns as $column)
		{
			if (array_key_exists($column['name'], $values))
			{
				$tmp[$column['name']] = $values[$column['name']];
			}
			// todo else nejaky zpusob jak rict o chybe, protoze nekdy to chyba byt muze, jindy ale ne
		}
		// todo dalsi ktere nejsou v tabulce muze byt taky chyba (ale nemusi)
		// todo vytvorit moznost zkontrolovat db, kde se budou kontrolovat jestli nejaky radek nechybi, nebo naopak nepribyva, jestli nekte nechyby NULL (nebo naopak), mozna i zkontrolovat default hodnoy, a typy
		$values = $tmp;

		if (isset($entity->id) AND $this->connection->fetch('SELECT [id] FROM %n WHERE [id] = %s', $table, $entity->id))
		{
			$id = $entity->id;
			$this->connection->update($table, $values)->where('[id] = %s', $id)->execute();
		}
		else
		{
			if (array_key_exists('id', $values) AND $values['id'] === NULL) unset($values['id']);
			$this->connection->insert($table, $values)->execute();
			try {
				$id = $this->connection->getInsertId();
			} catch (DibiException $e) {
				if (isset($values['id'])) $id = $values['id'];
				else if (isset($entity->id)) $id = $entity->id;
				else throw $e;
			}
		}

		return $id;
	}

}

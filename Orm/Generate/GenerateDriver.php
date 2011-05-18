<?php

namespace Orm;

use Nette\Object;
use Nette\InvalidStateException;
use Exception;
use DibiConnection;

interface IGenerateDriver
{

}

abstract class GenerateDriver extends Object implements IGenerateDriver
{
	private $result = array();

	protected $connection;

	protected $tableName;

	private $end = false;

	public function __construct(DibiConnection $connection, $tableName)
	{
		$dibiDriverName = 'Dibi' . substr(get_class($this), strlen(__CLASS__)) . 'Driver';
		if (!($connection->driver instanceof $dibiDriverName))
		{
			throw new InvalidStateException();
		}
		$this->connection = $connection;
		$this->tableName = $tableName;
		$this->addHeader();
	}

	public static function getDriverClassName($connection)
	{
		$dibiDriverName = get_class($connection->driver);
		$driverName = __CLASS__ . substr($dibiDriverName, strlen('Dibi'), strlen($dibiDriverName) - strlen('DibiDriver'));
		if (!class_exists($driverName))
		{
			throw new InvalidStateException($driverName);
		}
		$implements = class_implements($driverName);
		if (!isset($implements['Orm\IGenerateDriver']))
		{
			throw new InvalidStateException($driverName);
		}
		return $driverName;
	}

	abstract protected function addHeader();
	abstract protected function addFooter();

	protected function line()
	{
		if ($this->end) throw new Exception();
		$args = func_get_args();
		$this->result[] = call_user_func_array(array($this, 'connectionTranslate'), $args);
	}

	/**
	 * Use DibiConnection::translate() or DibiConnection::sql()
	 */
	protected function connectionTranslate($args)
	{
		$args = func_get_args();
		$connection = $this->connection;
		return call_user_func_array(array($connection, method_exists($connection, 'translate') ? 'translate' : 'sql'), $args);
	}

	public function getSql()
	{
		$this->addFooter();
		$this->end = true;
		return "\n".implode("\n", $this->result)."\n";
	}
}

<?php

use Orm\NotSupportedException;

class DibiMockEscapeMySqlDriver extends DibiMySqlDriver
{

	/**
	 * Encodes data for use in a SQL statement.
	 * @param  mixed     value
	 * @param  string    type (dibi::TEXT, dibi::BOOL, ...)
	 * @return string    encoded value
	 * @throws InvalidArgumentException
	 */
	public function escape($value, $type)
	{
		switch ($type) {
		case dibi::TEXT:
			return "'" . $this->mysql_real_escape_string($value) . "'";

		case dibi::BINARY:
			return "_binary'" . $this->mysql_real_escape_string($value) . "'";

		case dibi::IDENTIFIER:
			// @see http://dev.mysql.com/doc/refman/5.0/en/identifiers.html
			return '`' . str_replace('`', '``', $value) . '`';

		case dibi::BOOL:
			return $value ? 1 : 0;

		case dibi::DATE:
			return $value instanceof DateTime ? $value->format("'Y-m-d'") : date("'Y-m-d'", $value);

		case dibi::DATETIME:
			return $value instanceof DateTime ? $value->format("'Y-m-d H:i:s'") : date("'Y-m-d H:i:s'", $value);

		default:
			throw new InvalidArgumentException('Unsupported type.');
		}
	}

	private function mysql_real_escape_string($value)
	{
		if (is_array($value)) return array_map(array($this, __FUNCTION__), $value);
		if (!empty($value) AND is_string($value))
		{
			return str_replace(array(
				'\\', "\0", "\n", "\r", "'", '"', "\x1a"
			), array(
				'\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'
			), $value);
		}
		return $value;
	}

	public function __construct()
	{

	}

	public function connect(array &$config)
	{

	}

	public function disconnect()
	{

	}

	public function query($sql)
	{
		throw new NotSupportedException;
	}

	public function getInfo()
	{
		throw new NotSupportedException;
	}

	public function getAffectedRows()
	{
		throw new NotSupportedException;
	}

	public function getInsertId($sequence)
	{
		throw new NotSupportedException;
	}

	public function begin($savepoint = NULL)
	{
		throw new NotSupportedException;
	}

	public function commit($savepoint = NULL)
	{
		throw new NotSupportedException;
	}

	public function rollback($savepoint = NULL)
	{
		throw new NotSupportedException;
	}

	public function getResource()
	{
		return $this;
	}

	public function getReflector()
	{
		throw new NotSupportedException;
	}

	public function createResultDriver($resource)
	{
		throw new NotSupportedException;
	}

	public function __destruct()
	{

	}

	public function getRowCount()
	{
		throw new NotSupportedException;
	}

	public function fetch($assoc)
	{
		throw new NotSupportedException;
	}

	public function seek($row)
	{
		throw new NotSupportedException;
	}

	public function free()
	{
		throw new NotSupportedException;
	}

	public function getResultColumns()
	{
		throw new NotSupportedException;
	}

	public function getResultResource()
	{
		throw new NotSupportedException;
	}

}

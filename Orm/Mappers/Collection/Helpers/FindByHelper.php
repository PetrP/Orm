<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use DateTime;

/**
 * Helper for findBy operations.
 * @author Petr Procházka
 * @package Orm
 * @subpackage Mappers\Collection\Helpers
 */
class FindByHelper
{

	/**
	 * @param string reference
	 * @param array reference
	 * @return bool
	 * @throws InvalidArgumentException
	 */
	public static function parse(& $name, array & $args)
	{
		$mode = $by = NULL;
		if (strncasecmp($name, 'findBy', 6) === 0)
		{
			$mode = 'findBy';
			$by = substr($name, 6);
		}
		else if (strncasecmp($name, 'getBy', 5) === 0)
		{
			$mode = 'getBy';
			$by = substr($name, 5);
		}

		if ($mode AND $by)
		{
			$where = array();
			foreach (explode('And', $by) as $n => $key)
			{
				if ($key{0} != "_") $key{0} = $key{0} | "\x20"; // lcfirst
				if (!array_key_exists($n, $args))
				{
					throw new InvalidArgumentException("There is no value for '$key' in '$name'.");
				}
				$where[$key] = $args[$n];
				unset($args[$n]);
			}
			if (count($args))
			{
				throw new InvalidArgumentException("There is extra value in '$name'.");
			}
			$name = $mode;
			$args = $where;
			return true;
		}

		return false;
	}

	/**
	 * Z $findBy prevede do $where v dibi formatu, $findBy vyprazdni.
	 * @param BaseDibiCollection
	 * @param DibiMapper
	 * @param IDatabaseConventional
	 * @param array reference
	 * @param array reference
	 * @param string
	 * @param string
	 * @see DataSourceCollection::getDataSource
	 * @see DibiCollection::__toString
	 * @see DibiCollection::join
	 */
	public static function dibiProcess(BaseDibiCollection $collection, DibiMapper $mapper, IDatabaseConventional $conventional, array & $where, array & $findBy, $tableAlias, $prefix = NULL)
	{
		foreach ($findBy as $tmp)
		foreach ($tmp as $key => $value)
		{
			if ($prefix) $key = $prefix . '->' . $key;
			if ($join = $mapper->getJoinInfo($key))
			{
				$collection->join($key);
				$key = $join->key;
			}
			else
			{
				$key = $conventional->formatEntityToStorage(array($key => NULL));
				$key =  $tableAlias . key($key);
			}
			if ($value instanceof IEntityCollection)
			{
				$value = $value->fetchPairs(NULL, 'id');
			}
			if ($value instanceof IEntity)
			{
				$value = isset($value->id) ? $value->id : NULL;
			}
			if (is_array($value))
			{
				$value = array_unique(
					array_map(function ($v) {
						if ($v instanceof \Orm\IEntity)
						{
							return isset($v->id) ? $v->id : NULL;
						}
						return $v;
					}, $value)
				);
				$where[] = array('%n IN %in', $key, $value);
			}
			else if ($value === NULL)
			{
				$where[] = array('%n IS NULL', $key);
			}
			else if ($value instanceof DateTime)
			{
				$where[] = array('%n = %t', $key, $value);
			}
			else
			{
				$where[] = array('%n = %s', $key, $value);
			}
		}
		$findBy = array();
	}

}

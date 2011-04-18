<?php

class FindByHelper
{

	public static function parse(& $name, array & $args)
	{
		$mode = $by = NULL;
		if (substr($name, 0, 6) === 'findBy')
		{
			$mode = 'find';
			$by = substr($name, 6);
		}
		else if (substr($name, 0, 5) === 'getBy')
		{
			$mode = 'get';
			$by = substr($name, 5);
		}

		if ($mode AND $by)
		{
			$where = array();
			foreach (explode('And', $by) as $n => $key)
			{
				if ($key{0} != "_") $key{0} = $key{0} | "\x20"; // lcfirst
				if (!array_key_exists($n, $args)) throw new InvalidArgumentException("There is no value for '$key' in '$name'.");
				$where[$key] = $args[$n];
				unset($args[$n]);
			}
			if (count($args))
			{
				throw new InvalidArgumentException("There is extra value in '$name'.");
			}
			$name = $mode === 'get' ? 'getBy' : 'findBy';
			$args = $where;
			return true;
		}

		return false;
	}

	public static function dibiProcess(DibiCollection $collection, DibiMapper $mapper, IConventional $conventional, array & $where, array & $findBy, $prefix = NULL)
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
				$key = key($conventional->formatEntityToStorage(array($key => NULL)));
				$key =  'e.' . $key;
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
					array_map(
						create_function('$v', 'return $v instanceof IEntity ? (isset($v->id) ? $v->id : NULL) : $v;'),
						$value
					)
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

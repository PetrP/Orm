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

}

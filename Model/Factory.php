<?php

class Factory extends Object
{
	static $models = array();
	
	public function init($models)
	{
		foreach ($models as $k => $v)
		{
			if (is_int($k))
			{
				if (!class_exists($v)) throw new InvalidStateException();
				self::$models[$v] = array(
					'entity' => $v,
					'repository' => class_exists($v . 'Repository'),
					'mapper' => $v . 'Mapper',
				);
			}
		}
	}
	
	private static $entities = array();
	private static $repositories = array();
	
	public static function getRepository($name)
	{
		$class = self::getRepositoryClass($name);
		if (class_exists($class))
		{
			return new $class;
		}
		else
		{
			$names = self::getNames($name);
			return new SimpleRepository($names['name']);
		}
		
	}
	
	public static function getEntityClass($by)
	{
		$names = self::getNames($by);
		return $names['entity'];
	}
	
	public static function getRepositoryClass($by)
	{
		$names = self::getNames($by);
		return $names['repository'];
	}
	
	public static function getMapperClass($by)
	{
		$names = self::getNames($by);
		return $names['mapper'];
	}
	
	public static function getName($by)
	{
		$names = self::getNames($by);
		return $names['name'];
	}
	
	private static function getNames($by)
	{
		static $cache = array();
		if (is_string($by))
		{
			$c = $by;
		}
		else if ($by instanceof Object)
		{
			$c = get_class($by);
		}
		else throw new InvalidStateException;

		if (!isset($cache[$c]))
		{
			$name = NULL;
			if (is_string($by))
			{
				$name = $by;
			}
			else if ($by instanceof Entity)
			{
				$name = get_class($by);
			}
			else if ($by instanceof Repository AND strpos(get_class($by), 'Repository') === 0)
			{
				$name = substr(get_class($by), 0, 0-strlen('Repository'));
			}

			
			if (!$name OR !class_exists($name))
			{
				throw new InvalidStateException($name);
			}
			
			$name{0} = strtolower($name{0});

			if (!isset($cache[$name]))
			{
				$cache[$name] = array(
					'name' => $name,
					'entity' => ucfirst($name),
					'repository' => ucfirst($name) . 'Repository',
					'mapper' => ucfirst($name) . 'Mapper',
				);
			}
			
			$cache[$c] = & $cache[$name];
		}
		return $cache[$c];
	}
	
}
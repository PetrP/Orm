<?php

class Factory extends Object
{
	
	private static $entities = array();
	private static $repositories = array();
	
	public static function getRepository($name)
	{
		$class = self::getRepositoryClass($name);
		//if (class_exists($class))
		//{
			return new $class;
		//}
		
	}
	
	public static function getEntityClass($name)
	{
		return self::getName($name);
	}
	
	public static function getRepositoryClass($name)
	{
		return self::getName($name). 'Repository';
	}
	
	public static function getMapperClass($name)
	{
		return self::getName($name). 'Mapper';
	}
	
	public static function getName($by)
	{
		$name = NULL;
		if ($by instanceof Repository AND strpos(get_class($by), 'Repository') === 0)
		{
			$name = substr(get_class($by), 0, 0-strlen('Repository'));
		}
		else if (is_string($by))
		{
			$name = $by;
		}
		
		if (!$name OR !class_exists($name))
		{
			throw new InvalidStateException();
		}
		
		return $name;
	}
	
}
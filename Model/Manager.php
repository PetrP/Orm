<?php

class Manager extends Object
{
	static $cache = array();
	public static function getEntityParams($name)
	{
		$class = Factory::getEntityClass($name);
		
		if (!isset(self::$cache[$class]))
		{
			$_class = $class;
			
			$params = array();
			
			while (class_exists($_class) AND $_class !== 'Entity')
			{
				$annotations = AnnotationsParser::getAll(new ClassReflection($_class));
				$_class = get_parent_class($_class);
				
				if (isset($annotations['property']))
				{
					foreach ($annotations['property'] as $property)
					{
						
						if (preg_match('#^(-read|-write)?\s?([a-z0-9_\/]+)\s+\$([a-z0-9_]+)($|\s)#si', $property, $match))
						{
							$property = $match[3];
							$type = $match[2];
							$mode = $match[1];
						}
						else if (preg_match('#^(-read|-write)?\s?\$([a-z0-9_]+)\s+([a-z0-9_\/]+)($|\s)#si', $property, $match))
						{
							$property = $match[2];
							$type = $match[3];
							$mode = $match[1];
						}
						else if (preg_match('#^(-read|-write)?\s?\$([a-z0-9_]+)($|\s)#si', $property, $match))
						{
							$property = $match[2];
							$type = 'mixed';
							$mode = $match[1];
						}
						else
						{
							continue;
						}
						
						if (!$mode OR $mode === '-read')
						{
							$params[$property]['get'] = array('method' => NULL , 'type' => strtolower($type));
						}
						if (!$mode OR $mode === '-write')
						{
							$params[$property]['set'] = array('method' => NULL , 'type' => strtolower($type));
						}
						
					}
				}
				
				if (isset($annotations['method']))
				{
					foreach ($annotations['method'] as $method)
					{
						
					}
				}
			}
			
			$methods = array_diff(get_class_methods($class), get_class_methods('Entity'));
			foreach ($methods as $method)
			{
				$m = substr($method, 0, 3);
				if ($m === 'get' OR $m === 'set')
				{
					$var = substr($method, 3);
					$var{0} = strtolower($var{0});
					if (isset($params[$var][$m]))
					{
						$params[$var][$m]['method'] = $method;
					}
					else
					{
						//$params[$var][$m] = array('method' => $method , 'type' => 'mixed');
					}
				}
			}
			
			self::$cache[$class] = $params;
		}
		
		return self::$cache[$class];
	}
	
	public static function isParamValid($types, & $value)
	{
		$_value = $value;

		foreach (array_reverse(explode('|', $types)) as $type)
		{
			if ($type === 'mixed') return true;
			else if ($type === 'void' OR $type === 'null')
			{
				if ($value === NULL) return true;
				continue;
			}
			else if (!in_array($type, array('string', 'float', 'int', 'bool', 'array', 'object')))
			{
				if ($value instanceof $type) return true;
				continue;
			}
			else
			{
				if (call_user_func("is_$type", $value)) return true;
				else
				{
					if (in_array($type, array('float', 'int')) AND is_numeric($value))
					{
						$_value = $value;
						settype($_value, $type);
					}
					else if (in_array($type, array('array', 'object')) AND (is_array($value) OR is_object($value)))
					{
						$_value = $value;
						settype($_value, $type);
					}
					else if ($type === 'string' AND (is_int($value) OR is_float($value) OR (is_object($value) AND method_exists($value, '__toString'))))
					{
						$_value = (string) $value;
					}
					else if ($type === 'bool')
					{
						$_value = (bool) $value;
					}
					continue;
				}
			}
		
		}
		
		if ($_value === $value)
		{
			return false;
		}
		else
		{
			$value = $_value;
			return true;
		}
		
	}
	
}
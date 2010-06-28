<?php

class EntityManager extends Object
{
	public static function getEntityParams($class)
	{
		if (!class_exists($class)) throw new InvalidStateException();
		else if (!is_subclass_of($class, 'Entity')) throw new InvalidStateException();

		$params = array();
		$classes = array();
		$_class = $class;
		while (class_exists($_class))
		{
			$classes[] = $_class;
			if ($_class === 'Entity') break;
			$_class = get_parent_class($_class);
		}

		foreach (array_reverse($classes) as $_class)
		{
			$annotations = AnnotationsParser::getAll(new ClassReflection($_class));

			if (isset($annotations['property']))
			{
				foreach ($annotations['property'] as $property)
				{
					if (preg_match('#^(-read|-write)?\s?([a-z0-9_\|]+)\s+\$([a-z0-9_]+)($|\s)#si', $property, $match))
					{
						$property = $match[3];
						$type = $match[2];
						$mode = $match[1];
					}
					else if (preg_match('#^(-read|-write)?\s?\$([a-z0-9_]+)\s+([a-z0-9_\|]+)($|\s)#si', $property, $match))
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
						throw new InvalidStateException($property);
						//continue;
					}

					if (isset($params[$property]['since']) AND $params[$property]['since'] !== $_class)
					{
						unset($params[$property]);
					}

					$type = explode('|',strtolower($type));
					if (in_array('mixed', $type))
					{
						$type = array();
					}

					if (isset($params[$property]['types']) AND isset($params[$property]['types']) AND $params[$property]['types'] !== $type)
					{
						throw new InvalidStateException('Getter and setter types must be same.');
					}

					$params[$property]['types'] = $type;

					if (!$mode OR $mode === '-read')
					{
						$params[$property]['get'] = array('method' => NULL);
						$params[$property]['since'] = $_class;
					}
					if (!$mode OR $mode === '-write')
					{
						$params[$property]['set'] = array('method' => NULL);
						$params[$property]['since'] = $_class;
					}

				}
			}

			if (isset($annotations['fk']))
			{
				if (isset($annotations['foreignKey']))
				{
					$annotations['foreignKey'] = array_merge($annotations['foreignKey'], $annotations['fk']);
				}
				else
				{
					$annotations['foreignKey'] = $annotations['fk'];
				}
			}
			if (isset($annotations['foreignKey']))
			{
				foreach ($annotations['foreignKey'] as $fk)
				{
					if (preg_match('#\s?\$([a-z0-9_]+)\s([a-z0-9_]+)$#si', $fk, $match))
					{
						$property = $match[1];
						$repository = $match[2];
						if (isset($params[$property]))
						{
							if (Model::getRepository($repository) instanceof Repository)
							{
								$params[$property]['fk'] = $repository;
							}
							else throw new InvalidStateException($repository);
						}
						else throw new InvalidStateException($property);
					}
					else throw new InvalidStateException();
				}
			}

			/*if (isset($annotations['method']))
			{
				foreach ($annotations['method'] as $method)
				{

				}
			}*/
		}

		$methods = array_diff(get_class_methods($class), get_class_methods('Entity'));
		foreach ($methods as $method)
		{
			$m = substr($method, 0, 3);
			if ($m === 'get' OR $m === 'set')
			{
				$var = substr($method, 3);
				if ($var{0} != '_') $var{0} = $var{0} | "\x20"; // lcfirst

				if (isset($params[$var][$m]))
				{
					$params[$var][$m]['method'] = $method;
				}
			}
		}
		
		return $params;
	}

}

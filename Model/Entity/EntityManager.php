<?php

class EntityManager extends Object // rename AnotationMetaDataZiskavac
{
	public static function getEntityParams($class)
	{
		if (!class_exists($class)) throw new InvalidStateException();
		$implements = class_implements($class);
		if (!isset($implements['IEntity'])) throw new InvalidStateException();

		$metaData = new MetaData($class);
		$params = array();
		$classes = array();
		$_class = $class;
		while (class_exists($_class))
		{
			if ($_class === 'Object') break;
			$classes[] = $_class;
			if ($_class === 'Entity') break; // todo
			$_class = get_parent_class($_class);
		}

		foreach (array_reverse($classes) as $_class)
		{
			$annotations = AnnotationsParser::getAll(new ClassReflection($_class));

			if (isset($annotations['property']))
			{
				foreach ($annotations['property'] as $string)
				{
					if (preg_match('#^(-read|-write)?\s?([a-z0-9_\|]+)\s+\$([a-z0-9_]+)($|\s(.*)$)#si', $string, $match))
					{
						$property = $match[3];
						$type = $match[2];
						$mode = $match[1];
						$string = $match[4];
					}
					else if (preg_match('#^(-read|-write)?\s?\$([a-z0-9_]+)\s+([a-z0-9_\|]+)($|\s(.*)$)#si', $string, $match))
					{
						$property = $match[2];
						$type = $match[3];
						$mode = $match[1];
						$string = $match[4];
					}
					else if (preg_match('#^(-read|-write)?\s?\$([a-z0-9_]+)($|\s(.*)$)#si', $string, $match))
					{
						$property = $match[2];
						$type = 'mixed';
						$mode = $match[1];
						$string = $match[3];
					}
					else
					{
						throw new InvalidStateException($string);
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
					$params[$property]['relationship'] = NULL;
					$params[$property]['relationshipParam'] = NULL;
					
					// todo cele prepsat a kontrolovat vsechny {* }

					if (preg_match('#\{\s*(OneToOne|1\:1)\s+([^\s]*)\s*\}#si', $string, $match))
					{
						$params[$property]['relationship'] = MetaData::OneToOne;
						$params[$property]['relationshipParam'] = $match[2];
					}
					else if (preg_match('#\{\s*(ManyToOne|(?:m|n)\:1)\s+([^\s]*)\s*\}#si', $string, $match))
					{
						$params[$property]['relationship'] = MetaData::ManyToOne;
						$params[$property]['relationshipParam'] = $match[2];
					}
					else if (preg_match('#\{\s*(ManyToMany|(?:m|n)\:(?:m|n))\s*}#si', $string, $match))
					{
						$params[$property]['relationship'] = MetaData::ManyToMany;
					}
					else if (preg_match('#\{\s*(OneToMany|1\:(?:m|n))\s*}#si', $string, $match))
					{
						$params[$property]['relationship'] = MetaData::OneToMany;
					}

					if (preg_match('#\{\s*enum\s+([^\}]+)\s*\}#si', $string, $match))
					{
						
						if (preg_match('#^([a-z0-9_-]+::[a-z0-9_-]+)\(\)$#si', trim($match[1]), $tmp))
						{
							$original = $enum = array_keys(callback($tmp[1])->invoke());
						}
						else
						{
							$original = array_map('trim', explode(',', $match[1]));
							$enum = array();
							foreach ($original as $d)
							{
								if (substr($d, 0, 6) === 'self::')
								{
									$d = str_replace('self::', "$class::", $d);
								}

								if (is_numeric($d))
								{
									$d = (float) $d;
								}
								else if (defined($d))
								{
									$d = constant($d);
								}
								else if (strpos($d, '::') !== false)
								{
									throw new Exception();
								}
								$enum[] = $d;
							}
						}
						$params[$property]['enum'] = array('constants' => $enum, 'original' => implode(', ', $original));
					}

					if (preg_match('#\{\s*default\s+([^\}]+)\s*\}#si', $string, $match))
					{
						$d = trim($match[1]);
						if (substr($d, 0, 6) === 'self::')
						{
							$d = str_replace('self::', "$class::", $d);
						}

						if (is_numeric($d))
						{
							$d = (float) $d;
						}
						else if (defined($d))
						{
							$d = constant($d);
						}
						else if (strpos($d, '::') !== false)
						{
							throw new Exception();
						}
						$params[$property]['default'] = $d;
					}

					if (!$mode OR $mode === '-read')
					{
						$params[$property]['get'] = true;
						$params[$property]['since'] = $_class;
					}
					if (!$mode OR $mode === '-write')
					{
						$params[$property]['set'] = true;
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
							if (!isset($params[$property]['relationship']))
							{
								if (Model::get()->isRepository($repository))
								{
									$params[$property]['relationship'] = MetaData::OneToOne;
									$params[$property]['relationshipParam'] = $repository;
								}
								else throw new InvalidStateException("$repository isn't repository in $property");
							}
							else throw new InvalidStateException("Already has relationship in $property");
						}
						else throw new InvalidStateException("$property not exists");
					}
					else throw new InvalidStateException("Bad fk format in $property.");
				}
			}

			/*if (isset($annotations['method']))
			{
				foreach ($annotations['method'] as $method)
				{

				}
			}*/
		}

		foreach ($params as $property => $param)
		{
			$metaData->add(
				$property,
				$param['types'],
				isset($param['get'], $param['set']) ? MetaData::READWRITE :
					(isset($param['get']) ? MetaData::READ : MetaData::WRITE)
				,
				isset($param['fk']) ? $param['fk'] : NULL,
				$param['since'],
				$param['relationship'],
				$param['relationshipParam'],
				isset($param['default']) ? $param['default'] : NULL,
				isset($param['enum']) ? $param['enum'] : NULL
			);
		}

		return $metaData->toArray();
	}

}


class MetaData extends Object
{
	const READ = 1;
	const WRITE = 2;
	const READWRITE = 3;

	const ManyToMany ='m:m';
	const OneToMany ='1:m';

	const ManyToOne ='m:1';
	const OneToOne ='1:1';

	private $entityClass;
	private $data = array();

	public function __construct($entityClass)
	{
		if ($entityClass instanceof IEntity)
		{
			$entityClass = get_class($entityClass);
		}
		else
		{
			if (!class_exists($entityClass)) throw new InvalidStateException();
			$implements = class_implements($entityClass);
			if (!isset($implements['IEntity'])) throw new InvalidStateException();
		}
		$this->entityClass = $entityClass;
	}

	public function add($name, $types = array(), $access = NULL, $fk = NULL, $since = NULL, $relationship = NULL, $relationshipParam = NULL, $default = NULL, array $enum = NULL)
	{
		if (isset($this->data[$name])) throw new Exception($name);

		if (!is_array($types))
		{
			$types = explode('|',strtolower($types));
			if (in_array('mixed', $types))
			{
				$types = array();
			}
		}
		if ($access === NULL) $access = self::READWRITE;

		if ($access === self::WRITE) throw new InvalidStateException("Neni mozne vytvaret write-only polozky: $name");

		if ($fk)
		{
			trigger_error(E_USER_DEPRECATED);
			$relationship = self::OneToMany;
			$relationshipParam = $fk;
		}

		if ($relationship === self::ManyToMany OR $relationship === self::OneToMany)
		{
			if (count($types) != 1) throw new InvalidStateException();
			$relationshipParam = current($types);
			if (!class_exists($relationshipParam)) throw new InvalidStateException();
			$parents = class_parents($relationshipParam);
			if (!isset($parents[$relationship === self::ManyToMany ? 'ManyToMany' : 'OneToMany'])) throw new InvalidStateException();
		}

		if ($relationship === self::ManyToOne OR $relationship === self::OneToOne)
		{
			if (!Model::get()->isRepository($relationshipParam))
			{
				throw new InvalidStateException("$relationshipParam isn't repository in $name");
			}
			$fk = $relationshipParam;
		}


		$this->data[$name] = array(
			'types' => $types,
			'get' => ($access === self::READ OR $access === self::READWRITE) ? array('method' => NULL) : NULL,
			'set' => ($access === self::WRITE OR $access === self::READWRITE) ? array('method' => NULL) : NULL,
			'fk' => $fk,
			'since' => $since,
			'relationship' => $relationship,
			'relationshipParam' => $relationshipParam,
			'default' => $default,
			'enum' => $enum,
		);
	}

	public function toArray()
	{
		$params = $this->data;

		$methods = array_diff(get_class_methods($this->entityClass), get_class_methods('Entity'));
		$methods[] = 'getId';
		// TODO neumoznuje pouzit vlastni IEntity
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

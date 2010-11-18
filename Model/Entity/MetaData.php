<?php

require_once dirname(__FILE__) . '/MetaDataProperty.php';

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

	private $properties = array();

	public function __construct($entityClass)
	{
		if ($entityClass instanceof IEntity)
		{
			$entityClass = get_class($entityClass);
		}
		else
		{
			if (!class_exists($entityClass)) throw new InvalidStateException();
			$r = new ClassReflection($entityClass);
			$entityClass = $r->getName();
			if (!$r->implementsInterface('IEntity')) throw new InvalidStateException();
		}
		$this->entityClass = $entityClass;
	}

	public function addProperty($name, $types, $access = MetaData::READWRITE, $since = NULL)
	{
		if (isset($this->properties[$name]))
		{
			if ($since === NULL OR $this->properties[$name]->getSince() === $since)
			{
				throw new Exception(); // todo
			}
			$this->properties[$name] = new MetaDataProperty($this, $name, $types, $access, $since);
		}
		else
		{
			$this->properties[$name] = new MetaDataProperty($this, $name, $types, $access, $since);
		}

		return $this->properties[$name];
	}

	public function getEntityClass()
	{
		return $this->entityClass;
	}

	public function toArray()
	{
		$properties = array();
		foreach ($this->properties as $name => $property)
		{
			$properties[$name] = $property->toArray();
		}

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
			}
			else if (substr($method, 0, 2) === 'is')
			{
				$m = 'get';
				$var = substr($method, 2);
				if ($var{0} != '_') $var{0} = $var{0} | "\x20"; // lcfirst
				if (!isset($properties[$var]) OR $properties[$var]['types'] !== array('bool'))
				{
					continue;
				}
			}
			else
			{
				continue;
			}

			if (isset($properties[$var][$m]))
			{
				$properties[$var][$m]['method'] = $method;
			}
		}

		return $properties;
	}
}

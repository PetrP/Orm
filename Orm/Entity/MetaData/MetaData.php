<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use Exception;
use ReflectionClass;

/**
 * Information about properties of entity.
 * @see MetaDataProperty
 * @author Petr Procházka
 * @package Orm
 * @subpackage Entity\MetaData
 */
class MetaData extends Object
{
	/** @see MetaDataProperty::setAccess() */
	const READ = 1;

	/** @see MetaDataProperty::setAccess() */
	const WRITE = 2;

	/** @see MetaDataProperty::setAccess() */
	const READWRITE = 3;

	/** @see MetaDataProperty::setManyToMany() */
	const ManyToMany = 'm:m';

	/** @see MetaDataProperty::setOneToMany() */
	const OneToMany = '1:m';

	/** @see MetaDataProperty::setManyToOne() */
	const ManyToOne = 'm:1';

	/** @see MetaDataProperty::setOneToOne() */
	const OneToOne = '1:1';

	/** @var string */
	private $propertyClass = 'Orm\MetaDataProperty';

	/** @var string Nazev entity ke ktere patri informace */
	private $entityClass;

	/** @var array */
	private $methods;

	/** @var array of MetaDataProperty Jednotlive parametry */
	private $properties = array();

	/**
	 * @param string|IEntity class name or object
	 * @param string|NULL null means default Orm\MetaDataProperty
	 */
	public function __construct($entityClass, $propertyClass = NULL)
	{
		if ($entityClass instanceof IEntity)
		{
			$entityClass = get_class($entityClass);
		}
		else
		{
			if (!class_exists($entityClass))
			{
				throw new InvalidArgumentException(array($this, '$entityClass', 'instance of Orm\IEntity', '', "; class '$entityClass' doesn't exists"));
			}
			$r = new ReflectionClass($entityClass);
			$entityClass = $r->getName();
			if (!$r->implementsInterface('Orm\IEntity'))
			{
				throw new InvalidArgumentException(array($this, '$entityClass', 'instance of Orm\IEntity', $entityClass));
			}
		}
		$this->entityClass = $entityClass;
		if ($propertyClass !== NULL)
		{
			if (!is_subclass_of($propertyClass, 'Orm\MetaDataProperty') AND strcasecmp($propertyClass, 'Orm\MetaDataProperty') !== 0)
			{
				throw new InvalidArgumentException(array($this, '$propertyClass', 'subclass of Orm\MetaDataProperty', $propertyClass));
			}
			$this->propertyClass = $propertyClass;
		}
	}

	/**
	 * Prida parametr.
	 * @param string
	 * @param array|string
	 * @param int MetaData::READ MetaData::READWRITE
	 * @param string|NULL internal Od jake entity tento parametr existuje.
	 * @return MetaDataProperty
	 */
	public function addProperty($name, $types, $access = MetaData::READWRITE, $since = NULL)
	{
		if (isset($this->properties[$name]))
		{
			if ($since === NULL)
			{
				throw new MetaDataException($this->getEntityClass() . "::\$$name already defined (use param \$since to redefine)");
			}
			else if ($this->properties[$name]->getSince() === $since)
			{
				throw new MetaDataException($this->getEntityClass() . "::\$$name is defined twice in $since");
			}
		}
		$this->properties[$name] = new $this->propertyClass($this, $name, $types, $access, $since);

		return $this->properties[$name];
	}

	/** @return string Nazev entity ke ktere patri informace */
	public function getEntityClass()
	{
		return $this->entityClass;
	}

	/**
	 * @param string property
	 * @return array get => string|NULL, set => string|NULL, is => string|NULL
	 */
	public function getMethods($name)
	{
		if ($this->methods === NULL)
		{
			static $excludeMethods;
			if ($excludeMethods === NULL)
			{
				// @codeCoverageIgnoreStart
				$excludeMethods = get_class_methods('Orm\BaseEntityFragment');
				// TODO neumoznuje pouzit vlastni IEntity
			}	// @codeCoverageIgnoreEnd

			$methods = array_diff(get_class_methods($this->entityClass), $excludeMethods);
			foreach ($methods as $method)
			{
				$var = NULL;
				$m = substr($method, 0, 3);
				if ($m === 'get' OR $m === 'set')
				{
					$var = substr($method, 3);
				}
				else if (strncmp($method, 'is', 2) === 0)
				{
					$m = 'is';
					$var = substr($method, 2);
				}
				if (!$var) continue;
				if ($var{0} != '_') $var{0} = $var{0} | "\x20"; // lcfirst
				$this->methods[$var][$m] = $method;
			}
		}
		static $default = array('get' => NULL, 'set' => NULL, 'is' => NULL);
		return (isset($this->methods[$name]) ? $this->methods[$name] + $default : $default);
	}

	/**
	 * internal format, ktery entita pouziva pro lepsi vykon.
	 * Take nacita informace o getterech a setterech
	 * @return array
	 */
	public function toArray()
	{
		$properties = array();
		foreach ($this->properties as $name => $property)
		{
			$properties[$name] = $property->toArray();
		}
		return $properties;
	}

	/** @param IRepositoryContainer */
	public function check(IRepositoryContainer $model)
	{
		foreach ($this->properties as $name => $property)
		{
			$property->check($model);
		}
	}

	/** @var array runtime cache */
	static private $cache = array();

	/** @var array runtime cache */
	static private $cache2 = array();

	/**
	 * Vraci MetaData v internal formatu.
	 * Entita ma metadata jako pole pro lepsi vykon.
	 * Vysledek se cachuje.
	 * @internal
	 * @param string class name
	 * @param IRepositoryContainer
	 * @return array internal format
	 */
	public static function getEntityRules($entityClass, IRepositoryContainer $model = NULL)
	{
		$lowerEntityClass = strtolower($entityClass);
		if ($model)
		{
			$hash = spl_object_hash($model);
			$cache = & self::$cache[$hash][$lowerEntityClass];
			if ($cache === NULL)
			{
				$cache2 = & self::$cache2[$hash][$lowerEntityClass];
				if ($cache2 !== NULL)
				{
					$cache2[0]->check($model);
					return $cache2[1];
				}
				if (isset(self::$cache[NULL][$lowerEntityClass][0]))
				{
					$cache2 = self::$cache[NULL][$lowerEntityClass];
					unset(self::$cache[NULL][$lowerEntityClass][0]); // muzu pouzit jen jednou protoze RelationshipLoader::check
				}
				else
				{
					$meta = self::createEntityRules($entityClass);
					$cache2 = array($meta, $meta->toArray());
				}
				try {
					$e = NULL;
					$cache2[0]->check($model);
					$cache = $cache2[1];
				} catch (Exception $e) {}
				unset(self::$cache2[$hash][$lowerEntityClass]);
				if (!self::$cache2[$hash]) unset(self::$cache2[$hash]);
				if ($e) throw $e;
			}
			return $cache;
		}
		else
		{
			$cache = & self::$cache[NULL][$lowerEntityClass];
			if ($cache === NULL)
			{
				$meta = self::createEntityRules($entityClass);
				$cache = array($meta, $meta->toArray());
			}
			return $cache[1];
		}
	}

	/**
	 * @param string
	 * @return MetaData
	 */
	private static function createEntityRules($entityClass)
	{
		if (!class_exists($entityClass)) throw new InvalidEntityException("Class '$entityClass' doesn`t exists");
		$implements = class_implements($entityClass);
		if (!isset($implements['Orm\IEntity'])) throw new InvalidEntityException("'$entityClass' isn`t instance of Orm\\IEntity");
		$meta = call_user_func(array($entityClass, 'createMetaData'), $entityClass);
		if (!($meta instanceof MetaData)) throw new BadReturnException(array($entityClass, 'createMetaData', 'Orm\MetaData', $meta));
		return $meta;
	}

	/** @deprecated */
	public static function clean()
	{
		self::$cache2 = self::$cache = array();
	}
}

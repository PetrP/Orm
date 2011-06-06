<?php

namespace Orm;

use Nette\Object;
use Nette\InvalidStateException;
use Exception;
use ReflectionClass;

require_once dirname(__FILE__) . '/MetaDataProperty.php';

/**
 * Informace o parametrech entity.
 */
class MetaData extends Object
{
	/**#@+ @see MetaDataProperty::setAccess() */
	const READ = 1;
	const WRITE = 2;
	const READWRITE = 3;
	/**#@-*/

	/** @see MetaDataProperty::setManyToMany() */
	const ManyToMany = 'm:m';
	/** @see MetaDataProperty::setOneToMany() */
	const OneToMany = '1:m';

	/** @see MetaDataProperty::setManyToOne() */
	const ManyToOne = 'm:1';
	/** @see MetaDataProperty::setOneToOne() */
	const OneToOne = '1:1';

	/** @var string Nazev entity ke ktere patri informace */
	private $entityClass;

	/** @var array */
	private $methods;

	/** @var array of MetaDataProperty Jednotlive parametry */
	private $properties = array();

	/** @param string string|IEntity class name or object */
	public function __construct($entityClass)
	{
		if ($entityClass instanceof IEntity)
		{
			$entityClass = get_class($entityClass);
		}
		else
		{
			if (!class_exists($entityClass)) throw new InvalidStateException("Class '$entityClass' doesn`t exists");
			$r = new ReflectionClass($entityClass);
			$entityClass = $r->getName();
			if (!$r->implementsInterface('Orm\IEntity')) throw new InvalidStateException("'$entityClass' isn`t instance of Orm\\IEntity");
		}
		$this->entityClass = $entityClass;
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
		if (!isset($this->methods))
		{
			$methods = array_diff(get_class_methods($this->entityClass), get_class_methods('Orm\_EntityBase'));
			// TODO neumoznuje pouzit vlastni IEntity
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
			if (!isset($cache))
			{
				$cache2 = & self::$cache2[$hash][$lowerEntityClass];
				if (isset($cache2))
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
			if (!isset($cache))
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
		if (!class_exists($entityClass)) throw new InvalidStateException("Class '$entityClass' doesn`t exists");
		$implements = class_implements($entityClass);
		if (!isset($implements['Orm\IEntity'])) throw new InvalidStateException("'$entityClass' isn`t instance of Orm\\IEntity");
		$meta = call_user_func(array($entityClass, 'createMetaData'), $entityClass);
		if (!($meta instanceof MetaData)) throw new InvalidStateException("It`s expected that 'Orm\\IEntity::createMetaData' will return 'Orm\\MetaData'.");
		return $meta;
	}

	/** @deprecated */
	public static function clean()
	{
		self::$cache2 = self::$cache = array();
	}
}

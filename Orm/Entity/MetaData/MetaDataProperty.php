<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use Closure;
use ReflectionClass;

/**
 * Information about one property of entity.
 * @see MetaData
 * @author Petr Procházka
 * @package Orm
 * @subpackage Entity\MetaData
 */
class MetaDataProperty extends Object
{
	/** @var string Nazev parametru */
	private $name;

	/** @var string Nazev entity */
	private $class;

	private $originalTypes;

	/** @var array informace */
	private $data = array(
		'types' => array(),
		'get' => NULL,
		'set' => NULL,
		'since' => NULL,
		'relationship' => NULL, // deprecated
		'relationshipParam' => NULL, // deprecated
		'default' => NULL,
		'enum' => NULL,
		'injection' => NULL,
	);

	/**
	 * @param MetaData
	 * @param string
	 * @param array|string
	 * @param int MetaData::READ MetaData::READWRITE
	 * @param string|NULL internal Od jake entity tento parametr existuje.
	 */
	public function __construct(MetaData $meta, $name, $types, $access = MetaData::READWRITE, $since = NULL)
	{
		$this->class = $meta->getEntityClass();
		if (!preg_match('#^[a-zA-Z0-9_]+$#', $name))
		{
			throw new MetaDataException("{$this->class} property name must be non-empty alphanumeric string, '$name' given");
		}
		$this->name = $name;
		$this->data['since'] = $since;
		$this->setTypes($types);
		$this->setAccess($access, $meta);
	}

	/**
	 * Pouziva se pro kontrolu napr. anotaci, jestli neni v jedne tride 2x stejnej parametr (coz je vetsinou chyba).
	 * Pri rucnim vytvareni MetaDat neni potreba vyplnovat, neni pak mozne 2x zadat stejny parametr.
	 * @internal
	 * @return string|NULL
	 */
	public function getSince()
	{
		return $this->data['since'];
	}

	/**
	 * Povolene typy.
	 * Pole nebo hodnoty rozdelene svislitkem |
	 *
	 * <pre>
	 * Napr.:
	 * string|int|float|bool|array|object
	 * NULL
	 * DateTime|JakakoliTrida
	 * mixed
	 * </pre>
	 *
	 * @param array|string
	 * @return MetaDataProperty $this
	 */
	protected function setTypes($types)
	{
		if (is_array($types))
		{
			$this->originalTypes = implode('|', $types);
			$types = $types;
		}
		else if (is_scalar($types))
		{
			$this->originalTypes = $types;
			$types = explode('|', $types);
		}

		static $alliases = array(
			'void' => 'null',
			'double' => 'float',
			'real' => 'float',
			'numeric' => 'float',
			'number' => 'float',
			'integer' => 'int',
			'boolean' => 'bool',
			'text' => 'string',
		);

		$tmp = array();
		foreach ($types as $k => $type)
		{
			$type = strtolower(trim($type));
			if (isset($alliases[$type]))
			{
				$type = $alliases[$type];
			}
			$tmp[$type] = $type;
		}
		$types = $tmp;

		if (isset($types['mixed']) OR $types === array('' => '') OR !$types) $types = array('mixed' => 'mixed', 'null' => 'null');
		unset($types['']);

		$this->data['types'] = $types;

		return $this;
	}

	/**
	 * Jestli je parametr ke cteni nebo jen pro zapis
	 * @param int MetaData::READ|MetaData::READWRITE
	 * @param MetaData
	 * @return MetaDataProperty $this
	 */
	protected function setAccess($access, MetaData $meta)
	{
		if ($access === NULL) $access = MetaData::READWRITE;
		if ($access === MetaData::WRITE) throw new MetaDataException("Neni mozne vytvaret write-only polozky: {$this->class}::\${$this->name}");
		if (!in_array($access, array(MetaData::READ, MetaData::READWRITE), true)) throw new MetaDataException(__CLASS__ . ' access is Orm\MetaData::READ or Orm\MetaData::READWRITE allowed');
		$methods = $meta->getMethods($this->name);
		if ($methods['is'] AND $this->data['types'] === array('bool' => 'bool'))
		{
			$methods['get'] = $methods['is'];
		}
		$this->data['get'] = $access & MetaData::READ ? array('method' => $methods['get']) : NULL;
		$this->data['set'] = $access & MetaData::WRITE ? array('method' => $methods['set']) : NULL;
		return $this;
	}

	/**
	 * Vytvoreni vstahu z jinou entitou.
	 * V anotaci lze zapsat i aliasem 1:1
	 * Mapper vetsinou uklada jako cizy klic.
	 *
	 * <pre>
	 * * @property Foo $foo {1:1 Foos}
	 * </pre>
	 *
	 * @param string Nazev repository pripojene polozky
	 * @return MetaDataProperty $this
	 */
	public function setOneToOne($repositoryName)
	{
		if (isset($this->data['relationship'])) throw new MetaDataException("Already has relationship in {$this->class}::\${$this->name}");
		if (!$repositoryName)
		{
			throw new MetaDataException("You must specify foreign repository in {$this->class}::\${$this->name}");
		}
		// todo kontrolovat jestli types obsahuje jen IEntity nebo NULL?

		$this->data['relationship'] = MetaData::OneToOne;
		$this->data['relationshipParam'] = $repositoryName;

		return $this;
	}

	/**
	 * Vytvoreni vstahu z jinou entitou.
	 * V anotaci lze zapsat i aliasem m:1
	 * Mapper vetsinou uklada jako cizy klic.
	 *
	 * <pre>
	 * * @property Foo $foo {m:1 Foos}
	 * </pre>
	 *
	 * @param string Nazev repository pripojene polozky
	 * @return MetaDataProperty $this
	 */
	public function setManyToOne($repositoryName)
	{
		$this->setOneToOne($repositoryName);
		$this->data['relationship'] = MetaData::ManyToOne;

		return $this;
	}

	/**
	 * @param MetaData::OneToMany|MetaData::ManyToMany
	 * @param string
	 * @param string
	 * @param mixed RelationshipLoader::MAPPED_HERE|RelationshipLoader::MAPPED_THERE|NULL
	 * @return MetaDataProperty $this
	 * @see self::setOneToMany()
	 * @see self::setManyToMany()
	 */
	private function setToMany($relationship, $repositoryName, $param, $mapped = NULL)
	{
		if (isset($this->data['relationship']))
		{
			throw new MetaDataException("Already has relationship in {$this->class}::\${$this->name}");
		}
		$mainClass = $relationship === MetaData::ManyToMany ? 'Orm\ManyToMany' : 'Orm\OneToMany';
		if (isset($this->data['types']['mixed']))
		{
			$this->setTypes($mainClass);
		}
		$class = $this->originalTypes;

		if (count($this->data['types']) != 1)
		{
			throw new MetaDataException("{$this->class}::\${$this->name} {{$relationship}} excepts $mainClass class as type, '$class' given");
		}


		$loader = new RelationshipLoader($relationship, $class, $repositoryName, $param, $this->class, $this->name, $mapped);
		$this->setInjection($loader);
		$this->data['relationship'] = $relationship;
		$this->data['relationshipParam'] = $loader;
		return $this;
	}

	/**
	 * Vytvoreni vstahu z jinou entitou.
	 * V anotaci lze zapsat i aliasem 1:m
	 * Mapper vetsinou uklada jako cizy klic na pripojenou entitu.
	 * Obsahuje tridu ktera je potomkem OneToMany
	 *
	 * <pre>
	 * * @property OneToMany $bars {1:m bars foo}
	 * * typ lze vynechat
	 * * @property $bars {1:m bars foo}
	 * * zpetna kompatibila
	 * * @property FooToBars $bars {1:m}
	 * </pre>
	 *
	 * @param string
	 * @param string parametr na child entitach (m:1)
	 *
	 * @return MetaDataProperty $this
	 * @see OneToMany
	 */
	public function setOneToMany($repositoryName = NULL, $param = NULL)
	{
		$this->setToMany(MetaData::OneToMany, $repositoryName, $param);
		return $this;
	}

	/**
	 * Vytvoreni vstahu z jinou entitou.
	 * V anotaci lze zapsat i aliasem m:n
	 * Mapper vetsinou uklada do propojovaci tabulky.
	 * Obsahuje tridu ktera je potomkem ManyToMany
	 *
	 * <pre>
	 * * @property ManyToMany $bars {m:n bars foos}
	 * * typ lze vynechat
	 * * @property $bars {m:n bars foos}
	 * * zpetna kompatibila
	 * * @property FoosToBars $bars {m:n}
	 * </pre>
	 *
	 * @param string
	 * @param string|NULL parametr na child entitach (m:m)
	 * @param mixed RelationshipLoader::MAPPED_HERE|RelationshipLoader::MAPPED_THERE|NULL
	 * @return MetaDataProperty $this
	 * @see ManyToMany
	 */
	public function setManyToMany($repositoryName = NULL, $param = NULL, $mapped = NULL)
	{
		$this->setToMany(MetaData::ManyToMany, $repositoryName, $param, $mapped);
		return $this;
	}

	/**
	 * Parametr muze byt jen jedna z techto hodnot.
	 * @param array
	 * @param string|NULL internal pouzije se pro zobrazeni v chybe (pri pouziti anotaci se pak v chybe zobrazuji nazvy konstant misto jejich hodnot)
	 * @return MetaDataProperty $this
	 * @see AnnotationMetaData::builtParamsEnum()
	 */
	public function setEnum(array $values, $original = NULL)
	{
		if ($original === NULL)
		{
			$original = implode(', ', array_map(function ($v) {
				if (is_string($v)) return "'$v'";
				if (is_object($v)) return get_class($v);
				if (is_bool($v)) return $v ? 'TRUE' : 'FALSE';
				if (is_scalar($v)) return $v;
				return gettype($v);
			}, $values));
		}
		$this->data['enum'] = array('constants' => $values, 'original' => $original);
		return $this;
	}

	/**
	 * Kdyz se parametr nevyplni pouzije se toto jako defaultni hodnota.
	 * Pri necem slozitejsim se muze vytvorit protected methoda na entite `getDefault<Param>` ktera vrati default hodnotu
	 * @param mixed
	 * @return MetaDataProperty $this
	 * @see AnnotationMetaData::builtParamsDefault()
	 */
	public function setDefault($value)
	{
		$this->data['default'] = $value;
		return $this;
	}

	/**
	 * Inject same class around value in entity.
	 *
	 * <code>
	 * 	$entity->foo = array('bar'); // call ArrayInjection::setInjectedValue(array('bar'))
	 * 	$entity->foo implements ArrayInjection;
	 *
	 * 	$repo->persist($entity) // call ArrayInjection::getInjectedValue()
	 * </code>
	 *
	 * Type must by class implements IEntityInjection.
	 * @param Callback|Closure|string|IEntityInjectionLoader|NULL
	 * null mean load from IEntityInjectionStaticLoader which is specify in type
	 * @return MetaDataProperty $this
	 * @see AnnotationMetaData::builtParamsInjection()
	 */
	public function setInjection($factory = NULL)
	{
		if (isset($this->data['injection']))
		{
			throw new MetaDataException("Already has injection in {$this->class}::\${$this->name}");
		}

		$class = $this->originalTypes;
		if (count($this->data['types']) != 1)
		{
			throw new MetaDataException("Injection expecte type as one class implements Orm\\IInjection, '{$class}' given in {$this->class}::\${$this->name}");
		}
		if (!class_exists($class))
		{
			throw new MetaDataException("Injection expecte type as class implements Orm\\IInjection, '{$class}' given in {$this->class}::\${$this->name}");
		}
		$reflection = new ReflectionClass($class);
		$class = $reflection->getName();

		if (!$reflection->implementsInterface('Orm\IEntityInjection'))
		{
			throw new MetaDataException("$class does not implements Orm\\IEntityInjection in {$this->class}::\${$this->name}");
		}
		if (!$reflection->isInstantiable())
		{
			throw new MetaDataException("$class is abstract or not instantiable in {$this->class}::\${$this->name}");
		}

		if ($factory instanceof IEntityInjectionLoader)
		{
			$factory = Callback::create($factory, 'create');
		}
		else if (Callback::is($factory) OR $factory instanceof Closure OR (is_string($factory) AND (strpos($factory, '::') OR strncmp($factory, "\0lambda_", 8) === 0)))
		{
			$factory = Callback::create($factory);
		}
		else if (!$factory AND $reflection->implementsInterface('Orm\IEntityInjectionStaticLoader'))
		{
			$factory = Callback::create($class, 'create');
		}
		else
		{
			if (!$factory)
			{
				throw new MetaDataException("There is not factory callback for injection in {$this->class}::\${$this->name}, specify one or use Orm\\IEntityInjectionStaticLoader");
			}
			$tmp = is_object($factory) ? get_class($factory) : (is_string($factory) ? $factory : gettype($factory));
			throw new MetaDataException("Injection expected valid callback, '$tmp' given in {$this->class}::\${$this->name}, specify one or use Orm\\IEntityInjectionStaticLoader");
		}

		$this->data['injection'] = InjectionFactory::create($factory, $class);

		return $this;
	}

	/**
	 * Zkontroluje asociace
	 * @param IRepositoryContainer
	 * @return void
	 * @see MetaData::check()
	 */
	public function check(IRepositoryContainer $model)
	{
		$relationship = $this->data['relationship'];
		if ($relationship === MetaData::OneToOne OR $relationship === MetaData::ManyToOne)
		{
			$repositoryName = $this->data['relationshipParam'];
			if (!$model->isRepository($repositoryName))
			{
				throw new MetaDataException("$repositoryName isn't repository in {$this->class}::\${$this->name}");
			}
		}
		else if ($relationship === MetaData::OneToMany OR $relationship === MetaData::ManyToMany)
		{
			$this->data['relationshipParam']->check($model);
		}
	}

	/**
	 * @return array internal format
	 * @see MetaData::toArray()
	 */
	public function toArray()
	{
		return $this->data;
	}

}

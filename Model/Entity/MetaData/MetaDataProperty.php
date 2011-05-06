<?php

require_once dirname(__FILE__) . '/../../Relationships/RelationshipLoader.php';

/**
 * Informace o jednom parametru
 * @see MetaData
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
			//'scalar' => '', todo
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
	 * @param MetaData::READ|MetaData::READWRITE
	 * @param MetaData
	 * @return MetaDataProperty $this
	 */
	protected function setAccess($access, MetaData $meta)
	{
		if ($access === NULL) $access = MetaData::READWRITE;
		if ($access === MetaData::WRITE) throw new InvalidStateException("Neni mozne vytvaret write-only polozky: {$this->class}::\${$this->name}");
		if (!in_array($access, array(MetaData::READ, MetaData::READWRITE), true)) throw new Exception();
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
		if (isset($this->data['relationship'])) throw new InvalidStateException("Already has relationship in {$this->class}::\${$this->name}");
		if (!$repositoryName)
		{
			throw new InvalidStateException("You must specify foreign repository in {$this->class}::\${$this->name}");
		}
		else if (!RepositoriesCollection::get()->isRepository($repositoryName)) // todo di
		{
			throw new InvalidStateException("$repositoryName isn't repository in {$this->class}::\${$this->name}");
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
		$this->setOnetoOne($repositoryName);
		$this->data['relationship'] = MetaData::ManyToOne;

		return $this;
	}

	/**
	 * @param MetaData::OneToMany|MetaData::ManyToMany
	 * @param string
	 * @param string
	 * @return MetaDataProperty $this
	 * @see self::setOneToMany()
	 * @see self::setManyToMany()
	 */
	private function setToMany($relationship, $repositoryName, $param, $mappedByThis = NULL)
	{
		if (isset($this->data['relationship']))
		{
			throw new InvalidStateException("Already has relationship in {$this->class}::\${$this->name}");
		}
		$mainClass = $relationship === MetaData::ManyToMany ? 'ManyToMany' : 'OneToMany';
		if (isset($this->data['types']['mixed']))
		{
			$this->setTypes($mainClass);
		}
		$class = $this->originalTypes;

		if (count($this->data['types']) != 1)
		{
			throw new InvalidStateException("{$this->class}::\${$this->name} {{$relationship}} excepts $mainClass class as type, '$class' given");
		}


		$loader = new RelationshipLoader($relationship, $class, $repositoryName, $param, $this->class, $this->name, $mappedByThis);
		$this->setInjection(callback($loader, 'create'));
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
	 * <pre>
	 * repositoryName paramName
	 * </pre>
	 *
	 * @param string
	 * @return array
	 * @see self::setOneToMany()
	 */
	public function builtParamsOneToMany($string, $slice = 2)
	{
		$string = preg_replace('#\s+#', ' ', trim($string));
		return array_slice(array_filter(array_map('trim', explode(' ', $string, 3))), 0, $slice) + array(NULL, NULL);
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
	 *
	 * @return MetaDataProperty $this
	 * @see ManyToMany
	 */
	public function setManyToMany($repositoryName = NULL, $param = NULL, $mappedByThis = NULL)
	{
		$this->setToMany(MetaData::ManyToMany, $repositoryName, $param, $mappedByThis);
		return $this;
	}

	/**
	 * <pre>
	 * repositoryName paramName
	 * repositoryName paramName mappedByThis
	 * repositoryName paramName map
	 * </pre>
	 *
	 * @param string
	 * @return array
	 * @see self::setManyToMany()
	 */
	public function builtParamsManyToMany($string)
	{
		$arr = $this->builtParamsOneToMany($string, 3);
		if (isset($arr[2]) AND stripos($arr[2], 'map') !== false)
		{
			$arr[2] = true;
		}
		else
		{
			$arr[2] = NULL;
		}
		return $arr;
	}

	/**
	 * Nahradi self:: za nazev entity
	 * @param string
	 * @return string
	 * @see self::setEnum()
	 * @see self::setDefault()
	 */
	private function builtSelf($string)
	{
		$string = trim($string);
		if (substr($string, 0, 6) === 'self::')
		{
			$string = str_replace('self::', "{$this->class}::", $string);
		}
		return $string;
	}

	/**
	 * Upravi vstupni parametry pro enum, kdyz jsou zadavany jako string (napr. v anotaci)
	 * Vytvori pole z hodnot rozdelenych carkou, umoznuje zapis konstant.
	 * Nebo umoznuje zavolat statickou tridu ktera vrati pole hodnot (pouzijou se klice)
	 *
	 * <pre>
	 * 1, 2, 3
	 * bla1, 'bla2', "bla3"
	 * TRUE, false, NULL, self::CONSTANT, Foo::CONSTANT
	 * self::tadyZiskejHodnoty()
	 * </pre>
	 *
	 * @param string
	 * @return array
	 * @see self::setEnum()
	 */
	public function builtParamsEnum($string)
	{
		if (preg_match('#^([a-z0-9_-]+::[a-z0-9_-]+)\(\)$#si', trim($string), $tmp))
		{
			$enum = callback($this->builtSelf($tmp[1]))->invoke();
			if (!is_array($enum)) throw new InvalidStateException("'{$this->class}' '{enum {$string}}': callback must return array, " . (is_object($enum) ? get_class($enum) : gettype($enum)) . ' given');
			$original = $enum = array_keys($enum);
		}
		else
		{
			$original = $enum = array();
			foreach (explode(',', $string) as $d)
			{
				$d = $this->builtSelf($d);

				if (is_numeric($d))
				{
					$value = (float) $d;
				}
				else if (defined($d))
				{
					$value = constant($d);
				}
				else if (strpos($d, '::') !== false)
				{
					throw new Exception();
				}
				else
				{
					$value = trim($d,'\'"'); // todo lepe?
				}
				$enum[] = $value;
				$original[] = $d;
			}
		}
		return array($enum, implode(', ', $original));
	}

	/**
	 * Upravi vstupni parametry pro default, kdyz jsou zadavany jako string (napr. v anotaci)
	 * Umoznuje zapsat konstantu.
	 *
	 * <pre>
	 * 568
	 * bla1
	 * TRUE
	 * self::CONSTANT
	 * Foo::CONSTANT
	 * </pre>
	 *
	 * @param mixed $string
	 * @return mixed
	 * @see self::setDefault()
	 */
	public function builtParamsDefault($string)
	{
		$string = $this->builtSelf($string);
		if (is_numeric($string))
		{
			$string = (float) $string;
		}
		else if (defined($string))
		{
			$string = constant($string);
		}
		else if (strpos($string, '::') !== false)
		{
			throw new Exception();
		}
		else
		{
			$string = trim($string, '\'"'); // todo lepe?
		}
		return array($string);
	}

	/**
	 * Parametr muze byt jen jedna z techto hodnot.
	 * @param array
	 * @param string|NULL internal pouzije se pro zobrazeni v chybe (pri pouziti anotaci se pak v chybe zobrazuji nazvy konstant misto jejich hodnot)
	 * @return MetaDataProperty $this
	 * @see self::builtParamsEnum()
	 */
	public function setEnum(array $values, $original = NULL)
	{
		$this->data['enum'] = array('constants' => array_unique($values), 'original' => $original ? $original : implode(', ', $values));
		// todo original zrusit

		return $this;
	}

	/**
	 * Kdyz se parametr nevyplni pouzije se toto jako defaultni hodnota.
	 * Pri necem slozitejsim se muze vytvorit protected methoda na entite `getDefault<Param>` ktera vrati default hodnotu
	 * @param mixed
	 * @return MetaDataProperty $this
	 * @see self::builtParamsDefault()
	 */
	public function setDefault($value)
	{
		$this->data['default'] = $value;

		return $this;
	}

	public function setInjection($factory)
	{
		if (isset($this->data['injection'])) throw new InvalidStateException("Already has injection in {$this->class}::\${$this->name}");

		$types = $this->data['types'];
		//unset($types['null']); // todo dava smysl aby mohl byt null? i kdyby ano tak v entityvalue je potreba nevytvaret injection kdyz je null a je mozne byt null
		if (count($types) != 1) throw new InvalidStateException(); // todo
		$class = current($types);
		if (!class_exists($class)) throw new Exception($class);
		$reflection = new ClassReflection($class);
		$class = $reflection->getName();

		if (!$reflection->implementsInterface('IEntityInjection')) throw new Exception("$class not implements IEntityInjection");
		if (!$reflection->isInstantiable()) throw new Exception("$class not instantiable");

		if ($factory instanceof Callback OR $factory instanceof Closure)
		{
			$factory = callback($factory);
		}
		else if (strpos($factory, '::'))
		{
			$factory = callback($this->builtSelf($factory));
		}
		else if (!$factory)
		{
			$factory = callback($class, 'create');
		}
		else throw new Exception();

		$this->data['injection'] = InjectionFactory::create($factory, $class);

		return $this;
	}

	/**
	 * @return array internal format
	 * @see MetaData::toArray()
	 */
	public function toArray()
	{
		if ($this->data['relationshipParam'] instanceof RelationshipLoader)
		{
			$this->data['relationshipParam']->check();
		}
		return $this->data;
	}

}

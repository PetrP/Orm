<?php

require_once dirname(__FILE__) . '/../Relationships/RelationshipLoader.php';

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
		'relationship' => NULL,
		'relationshipParam' => NULL,
		'default' => NULL,
		'enum' => NULL,
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
		$this->setAccess($access);
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
			$this->originalTypes = explode('|', $types);
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
			$type = strtolower($type);
			if (isset($alliases[$type]))
			{
				$type = $alliases[$type];
			}
			$tmp[$type] = $type;
		}
		$types = $tmp;

		if (isset($types['mixed'])) $types = array();

		$this->data['types'] = $types;

		return $this;
	}

	/**
	 * Jestli je parametr ke cteni nebo jen pro zapis
	 * @param MetaData::READ|MetaData::READWRITE
	 * @return MetaDataProperty $this
	 */
	protected function setAccess($access)
	{
		if ($access === NULL) $access = MetaData::READWRITE;
		if ($access === MetaData::WRITE) throw new InvalidStateException("Neni mozne vytvaret write-only polozky: {$this->class}::\${$this->name}");
		if (!in_array($access, array(MetaData::READ, MetaData::READWRITE), true)) throw new Exception();
		$this->data['get'] = $access & MetaData::READ ? array('method' => NULL) : NULL;
		$this->data['set'] = $access & MetaData::WRITE ? array('method' => NULL) : NULL;

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
		else if (!Model::get()->isRepository($repositoryName))
		{
			throw new InvalidStateException("$repositoryName isn't repository in {$this->class}::\${$this->name}");
		}

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
	 * @return MetaDataProperty $this
	 * @see self::setOneToMany()
	 * @see self::setManyToMany()
	 */
	private function setToMany($relationship)
	{
		if (isset($this->data['relationship'])) throw new InvalidStateException("Already has relationship in {$this->class}::\${$this->name}");
		if (count($this->data['types']) != 1) throw new InvalidStateException();
		$relationshipClassName = $this->originalTypes;
		$this->data['relationship'] = $relationship;
		$this->data['relationshipLoader'] = new RelationshipLoader($relationship, $relationshipClassName);
		return $this;
	}

	/**
	 * Vytvoreni vstahu z jinou entitou.
	 * V anotaci lze zapsat i aliasem 1:m
	 * Mapper vetsinou uklada jako cizy klic na pripojenou entitu.
	 * Obsahuje tridu ktera je potomkem OneToMany
	 *
	 * <pre>
	 * * @property FooToBars $bars {1:m}
	 * </pre>
	 *
	 * @return MetaDataProperty $this
	 * @see OneToMany
	 */
	public function setOneToMany()
	{
		$this->setToMany(MetaData::OneToMany);
		return $this;
	}

	/**
	 * Vytvoreni vstahu z jinou entitou.
	 * V anotaci lze zapsat i aliasem m:n
	 * Mapper vetsinou uklada do propojovaci tabulky.
	 * Obsahuje tridu ktera je potomkem ManyToMany
	 *
	 * <pre>
	 * * @property FoosToBars $bars {m:n}
	 * </pre>
	 *
	 * @return MetaDataProperty $this
	 * @see ManyToMany
	 */
	public function setManyToMany()
	{
		$this->setToMany(MetaData::ManyToMany);
		return $this;
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
			$original = $enum = array_keys(callback($this->builtSelf($tmp[1]))->invoke());
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

	/**
	 * @return array internal format
 	 * @see MetaData::toArray()
	 */
	public function toArray()
	{
		return $this->data;
	}

}

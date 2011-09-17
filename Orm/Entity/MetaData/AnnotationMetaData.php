<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use ReflectionClass;

/**
 * Fills MetaData from annotation.
 *
 * <code>
 * /**
 *  * @property int $id
 *  * @property string $param
 *  * @property float|NULL $price
 *  * @property DateTime $date {default now}
 *  * @property Bar $bar {m:1 Bars}
 *  * /
 * class Foo extends Entity
 * </code>
 * @author Petr Procházka
 * @package Orm
 * @subpackage Entity\MetaData
 */
class AnnotationMetaData extends Object
{

	/** @var array mozne aliasy method */
	static protected $aliases = array(
		'1:1' => 'onetoone',
		'm:1' => 'manytoone',
		'n:1' => 'manytoone',
		'm:m' => 'manytomany',
		'n:n' => 'manytomany',
		'm:n' => 'manytomany',
		'n:m' => 'manytomany',
		'1:m' => 'onetomany',
		'1:n' => 'onetomany',
	);

	static private $modes = array(
		'property' => MetaData::READWRITE,
		'property-read' => MetaData::READ,
		'property-write' => MetaData::WRITE,
	);

	/** @var AnnotationsParser */
	private $parser;

	/** @var string */
	private $class;

	/**
	 * temporary save
	 * @internal
	 * @var array|NULL array(string, MetaDataProperty)
	 * @see self::callOnMacro()
	 */
	private $property;

	/**
	 * Fill MetaData from annotation.
	 * @param MetaData|string|IEntity class name or object
	 * @param AnnotationsParser|NULL
	 * @return MetaData
	 */
	public static function getMetaData($metaData, AnnotationsParser $parser = NULL)
	{
		if (!($metaData instanceof MetaData))
		{
			$metaData = new MetaData($metaData);
		}
		if ($parser === NULL)
		{
			$parser = new AnnotationsParser();
		}
		new static($metaData, $parser);
		return $metaData;
	}

	/**
	 * @param MetaData
	 * @param AnnotationsParser
	 */
	protected function __construct(MetaData $metaData, AnnotationsParser $parser)
	{
		$this->parser = $parser;
		$this->class = $metaData->getEntityClass();

		foreach ($this->getClasses($this->class) as $class)
		{
			foreach ($this->getAnnotation($class) as $annotation => $tmp)
			{
				if (isset(self::$modes[$annotation]))
				{
					foreach ($tmp as $string)
					{
						$this->addProperty($metaData, $string, self::$modes[$annotation], $class);
					}
					continue;
				}

				if (strncasecmp($annotation, 'prop', 4) === 0)
				{
					$string = current($tmp);
					throw new AnnotationMetaDataException("Invalid annotation format '@$annotation $string' in $class");
				}
			}
		}
	}

	/**
	 * Returns phpdoc annotations.
	 * @param string class name
	 * @return array of annotation => array
	 * @see AnnotationsParser
	 */
	protected function getAnnotation($class)
	{
		return $this->parser->getByReflection(new ReflectionClass($class));
	}

	/**
	 * @param string
	 * @return array
	 */
	private function getClasses($class)
	{
		$classes = array($class);
		while ($class = get_parent_class($class))
		{
			$i = class_implements($class);
			if (!isset($i['Orm\IEntity']))
			{
				break;
			}
			$classes[] = $class;
			if ($class === 'Orm\Entity') // speedup
			{
				break;
			}
		}
		return array_reverse($classes);
	}

	/**
	 * @param MetaData
	 * @param string
	 * @param MetaData::READWRITE|MetaData::READ|MetaData::WRITE
	 * @param string
	 */
	private function addProperty(MetaData $metaData, $string, $mode, $class)
	{
		if ($mode === MetaData::READWRITE) // bc; drive AnnotationsParser na pomlcce zkoncil
		{
			if (preg_match('#^(-read|-write)?\s?(.*)$#si', $string, $match))
			{
				$mode = $match[1];
				$mode = ((!$mode OR $mode === '-read') ? MetaData::READ : 0) | ((!$mode OR $mode === '-write') ? MetaData::WRITE : 0);
				$string = $match[2];
			}
		}

		if (preg_match('#^([a-z0-9_\|\\\\]+)\s+\$([a-z0-9_]+)($|\s(.*)$)#si', $string, $match))
		{
			$property = $match[2];
			$type = $match[1];
			$string = $match[3];
		}
		else if (preg_match('#^\$([a-z0-9_]+)\s+([a-z0-9_\|\\\\]+)($|\s(.*)$)#si', $string, $match))
		{
			$property = $match[1];
			$type = $match[2];
			$string = $match[3];
		}
		else if (preg_match('#^\$([a-z0-9_]+)($|\s(.*)$)#si', $string, $match))
		{
			$property = $match[1];
			$type = 'mixed';
			$string = $match[2];
		}
		else
		{
			$tmp = $mode === MetaData::READ ? '-read' : '';
			throw new AnnotationMetaDataException("Invalid annotation format '@property$tmp $string' in $class");
		}

		$propertyName = $property;
		$property = $metaData->addProperty($propertyName, $type, $mode, $class);
		$this->property = array($propertyName, $property);
		$string = preg_replace_callback('#\{\s*([^\s\}\{]+)(?:\s+([^\}\{]*))?\s*\}#si', array($this, 'callOnMacro'), $string);
		$this->property = NULL;

		if (preg_match('#\{|\}#', $string))
		{
			$string = trim($string);
			throw new AnnotationMetaDataException("Invalid annotation format, extra curly bracket '$string' in $class::\$$propertyName");
		}
	}

	/**
	 * callback
	 * Vola metodu na property. To je cokoli v kudrnatych zavorkach.
	 * @internal
	 * @param array
	 * @see MetaDataProperty::$property
	 */
	private function callOnMacro($match)
	{
		list($propertyName, $property) = $this->property;

		$name = strtolower($match[1]);
		if (isset(static::$aliases[$name])) $name = static::$aliases[$name];
		$method = "set{$name}";
		if (!method_exists($property, $method))
		{
			$class = $property->getSince();
			throw new AnnotationMetaDataException("Unknown annotation macro '{{$match[1]}}' in $class::\$$propertyName");
		}
		$params = isset($match[2]) ? $match[2] : NULL;
		$paramMethod = "builtParams{$name}";
		if (method_exists($this, $paramMethod))
		{
			$params = $this->$paramMethod($params);
		}
		else
		{
			$params = array($params);
		}
		call_user_func_array(array($property, $method), $params);
	}

	/**
	 * <code>
	 * repositoryName paramName
	 * </code>
	 *
	 * @param string
	 * @param int internal
	 * @return array
	 * @see MetaDataProperty::setOneToMany()
	 */
	public function builtParamsOneToMany($string, $slice = 2)
	{
		$string = preg_replace('#\s+#', ' ', trim($string));
		return array_slice(array_filter(array_map('trim', explode(' ', $string, 3))), 0, $slice) + array(NULL, NULL);
	}

	/**
	 * <code>
	 * repositoryName paramName
	 * repositoryName paramName mappedByThis
	 * repositoryName paramName map
	 * </code>
	 *
	 * @param string
	 * @return array
	 * @see MetaDataProperty::setManyToMany()
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
	 * Upravi vstupni parametry pro enum, kdyz jsou zadavany jako string (napr. v anotaci)
	 * Vytvori pole z hodnot rozdelenych carkou, umoznuje zapis konstant.
	 * Nebo umoznuje zavolat statickou tridu ktera vrati pole hodnot (pouzijou se klice)
	 *
	 * <code>
	 * 1, 2, 3
	 * bla1, 'bla2', "bla3"
	 * TRUE, false, NULL, self::CONSTANT, Foo::CONSTANT
	 * self::tadyZiskejHodnoty()
	 * </code>
	 *
	 * @param string
	 * @return array
	 * @see MetaDataProperty::setEnum()
	 */
	public function builtParamsEnum($string)
	{
		if (preg_match('#^([a-z0-9_\\\\]+::[a-z0-9_]+)\(\)$#si', trim($string), $tmp))
		{
			$enum = Callback::create($this->parseSelf($tmp[1]))->invoke();
			if (!is_array($enum)) throw new AnnotationMetaDataException("'{$this->class}' '{enum {$string}}': callback must return array, " . (is_object($enum) ? get_class($enum) : gettype($enum)) . ' given');
			$original = $enum = array_keys($enum);
		}
		else
		{
			$original = $enum = array();
			foreach (explode(',', $string) as $d)
			{
				$d = $this->parseSelf($d);
				$value = $this->parseString($d, "{enum {$string}}");
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
	 * <code>
	 * 568
	 * bla1
	 * TRUE
	 * self::CONSTANT
	 * Foo::CONSTANT
	 * </code>
	 *
	 * @param string
	 * @return array
	 * @see MetaDataProperty::setDefault()
	 */
	public function builtParamsDefault($string)
	{
		$string = $this->parseSelf($string);
		$string = $this->parseString($string, "{default {$string}}");
		return array($string);
	}

	/**
	 * Umoznuje zapis self::method()
	 * @param mixed
	 * @return mixed
	 * @see MetaDataProperty::setInjection()
	 */
	public function builtParamsInjection($string)
	{
		return array(rtrim($this->parseSelf($string), '()'));
	}

	/**
	 * Nahradi self:: za nazev entity
	 * @param string
	 * @return string
	 * @see self::builtParamsEnum()
	 * @see self::builtParamsDefault()
	 * @see self::builtParamsInjection()
	 */
	protected function parseSelf($string)
	{
		$string = trim($string);
		if (substr($string, 0, 6) === 'self::')
		{
			$string = str_replace('self::', "{$this->class}::", $string);
		}
		return $string;
	}

	/**
	 * Na hodnutu konstanty, cislo nebo string
	 * @param string
	 * @param string
	 * @return scalar
	 * @see self::builtParamsEnum()
	 * @see self::builtParamsDefault()
	 */
	protected function parseString($value, $errorMessage)
	{
		if (is_numeric($value))
		{
			$value = (float) $value;
			$intValue = (int) $value;
			if ($intValue == $value)
			{
				$value = $intValue;
			}
		}
		else if (defined($value))
		{
			$value = constant($value);
		}
		else if (strpos($value, '::') !== false)
		{
			throw new AnnotationMetaDataException("'{$this->class}' '$errorMessage': Constant $value not exists");
		}
		else
		{
			$value = trim($value, '\'"'); // todo lepe?
		}
		return $value;
	}

}

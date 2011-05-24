<?php

namespace Orm;

use Nette\Object;
use Nette\Reflection\AnnotationsParser;
use Nette\InvalidStateException;
use Nette\InvalidArgumentException;
use Exception;
use ReflectionClass;

require_once dirname(__FILE__) . '/MetaData.php';

/**
 * Ziskava meta data z enotaci.
 *
 * <pre>
 * /**
 *  * @property int $id
 *  * @property string $param
 *  * @property float|NULL $price
 *  * @property DateTime $date {default now}
 *  * @property Bar $bar {m:1 Bars}
 *  *⁄
 * class Foo extends Entity
 * <pre>
 */
class AnnotationMetaData extends Object
{

	/** @var array mozne aliasy method */
	static private $aliases = array(
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

	/** @var MetaData */
	protected $metaData;

	/** @var string */
	private $class;

	/**
	 * temporary save
	 * @internal
	 * @var array|NULL array(string, MetaDataProperty)
	 * @see self::callOnProperty()
	 */
	private $property;

	/**
	 * @param string|IEntity class name or object
	 * @return MetaData
	 */
	public static function getMetaData($class)
	{
		$a = new self($class);
		return $a->metaData;
	}

	/** @param string */
	protected function __construct($class)
	{
		$this->metaData = new MetaData($class);
		$this->class = $this->metaData->getEntityClass();
		$this->process();
	}

	/**
	 * @param string
	 * @return array
	 */
	protected function getAnnotation($class)
	{
		return AnnotationsParser::getAll(new ReflectionClass($class));
	}

	/** @param array */
	private function getClasses()
	{
		$class = $this->class;
		$classes = array();
		while (class_exists($class))
		{
			if ($class === 'Nette\Object') break;
			$classes[] = $class;
			if ($class === 'Orm\Entity') break; // todo
			$class = get_parent_class($class);
		}
		return array_reverse($classes);
	}

	/**
	 * @param string
	 * @param MetaData::READWRITE|MetaData::READ|MetaData::WRITE
	 * @param string
	 */
	private function addProperty($string, $mode, $class)
	{
		if ($mode === MetaData::READWRITE) // bc; drive AnnotationsParser na pomlcce zkoncil
		{
			if (preg_match('#^(-read|-write)?\s?(.*)$#si', $string, $match))
			{
				$mode = $match[1];
				$mode = ((!$mode OR $mode === '-read') ? MetaData::READ : 0) | ((!$mode OR $mode === '-write') ? MetaData::WRITE : 0);
				$string = $match[2];
			}
			else
			{
				throw new InvalidStateException("Invalid annotation format '@property$string' in $class");
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
			throw new InvalidStateException("Invalid annotation format '@property$tmp $string' in $class");
		}

		$propertyName = $property;
		$property = $this->metaData->addProperty($propertyName, $type, $mode, $class);
		$this->property = array($propertyName, $property);
		$string = preg_replace_callback('#\{\s*([^\s\}\{]+)(?:\s+([^\}\{]*))?\s*\}#si', array($this, 'callOnProperty'), $string);
		$this->property = NULL;

		if (preg_match('#\{|\}#', $string))
		{
			$string = trim($string);
			throw new InvalidStateException("Invalid annotation format, extra curly bracket '$string' in $class::\$$propertyName");
		}
	}


	private function process()
	{
		static $modes = array(
			'property' => MetaData::READWRITE,
			'property-read' => MetaData::READ,
			'property-write' => MetaData::WRITE,
		);

		foreach ($this->getClasses() as $class)
		{
			foreach ($this->getAnnotation($class) as $annotation => $tmp)
			{
				if (isset($modes[$annotation]))
				{
					foreach ($tmp as $string)
					{
						$this->addProperty($string, $modes[$annotation], $class);
					}
					continue;
				}

				if (strncasecmp($annotation, 'prop', 4) === 0)
				{
					$string = current($tmp);
					throw new InvalidStateException("Invalid annotation format '@$annotation $string' in $class");
				}
			}
		}
	}

	/**
	 * callback
	 * Vola metodu na property. To je cokoli v kudrnatych zavorkach.
	 * @internal
	 * @param array
	 * @see MetaDataProperty::$property
	 */
	private function callOnProperty($match)
	{
		list($propertyName, $property) = $this->property;

		$name = strtolower($match[1]);
		if (isset(self::$aliases[$name])) $name = self::$aliases[$name];
		$method = "set{$name}";
		if (!method_exists($property, $method))
		{
			$class = $property->getSince();
			throw new InvalidStateException("Unknown annotation macro '{{$match[1]}}' in $class::\$$propertyName");
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
	 * <pre>
	 * repositoryName paramName
	 * </pre>
	 *
	 * @param string
	 * @return array
	 * @see MetaDataProperty::setOneToMany()
	 */
	public function builtParamsOneToMany($string, $slice = 2)
	{
		$string = preg_replace('#\s+#', ' ', trim($string));
		return array_slice(array_filter(array_map('trim', explode(' ', $string, 3))), 0, $slice) + array(NULL, NULL);
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
	 * Nahradi self:: za nazev entity
	 * @param string
	 * @return string
	 * @see self::builtParamsEnum()
	 * @see self::builtParamsDefault()
	 * @see self::builtParamsInjection()
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
	 * @see MetaDataProperty::setEnum()
	 */
	public function builtParamsEnum($string)
	{
		if (preg_match('#^([a-z0-9_\\\\]+::[a-z0-9_]+)\(\)$#si', trim($string), $tmp))
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
					throw new InvalidArgumentException("'{$this->class}' '{enum {$string}}': Constant $d not exists");
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
	 * @param string
	 * @return array
	 * @see MetaDataProperty::setDefault()
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
			throw new InvalidArgumentException("'{$this->class}' '{default {$string}}': Constant $string not exists");
		}
		else
		{
			$string = trim($string, '\'"'); // todo lepe?
		}
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
		return array(rtrim($this->builtSelf($string), '()'));
	}

}

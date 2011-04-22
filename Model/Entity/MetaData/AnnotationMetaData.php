<?php

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
		return AnnotationsParser::getAll(new ClassReflection($class));
	}

	/** @param array */
	private function getClasses()
	{
		$class = $this->class;
		$classes = array();
		while (class_exists($class))
		{
			if ($class === 'Object') break;
			$classes[] = $class;
			if ($class === 'Entity') break; // todo
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

		if (preg_match('#^([a-z0-9_\|]+)\s+\$([a-z0-9_]+)($|\s(.*)$)#si', $string, $match))
		{
			$property = $match[2];
			$type = $match[1];
			$string = $match[3];
		}
		else if (preg_match('#^\$([a-z0-9_]+)\s+([a-z0-9_\|]+)($|\s(.*)$)#si', $string, $match))
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

				if ($annotation === 'fk' OR $annotation === 'foreignKey')
				{
					throw new DeprecatedException("Annotation @fk and @foreignKey is deprecated use {1:1 repo} instead; in {$class}.");
				}
			}
		}
	}

	/**
	 * callback
	 * Vola metodu na property. To je cokoli v kudrnatych zavorkach.
	 * @internal
	 * @param array
	 * @see self::$property
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
		if (method_exists($property, $paramMethod))
		{
			$params = $property->$paramMethod($params);
		}
		else
		{
			$params = array($params);
		}
		call_user_func_array(array($property, $method), $params);
	}

}

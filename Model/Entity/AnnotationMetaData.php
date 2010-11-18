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

	/**
	 * temporary save
	 * @internal
	 * @var MetaDataProperty|NULL
	 * @see self::callOnProperty()
	 */
	static private $property;

	/**
	 * @param string|IEntity class name or object
	 * @return MetaData
	 */
	public static function getEntityParams($class) // todo rename na getMetaData
	{
		$metaData = new MetaData($class);
		$class = $metaData->getEntityClass();
		$classes = array();
		while (class_exists($class))
		{
			if ($class === 'Object') break;
			$classes[] = $class;
			if ($class === 'Entity') break; // todo
			$class = get_parent_class($class);
		}

		foreach (array_reverse($classes) as $class)
		{
			$annotations = AnnotationsParser::getAll(new ClassReflection($class));

			foreach (array(
				MetaData::READWRITE => isset($annotations['property']) ? $annotations['property'] : array(),
				MetaData::READ => isset($annotations['property-read']) ? $annotations['property-read'] : array(),
				MetaData::WRITE => isset($annotations['property-write']) ? $annotations['property-write'] : array(),
			) as $_mode => $annotation)
			{
				foreach ($annotation as $string)
				{
					$mode = $_mode;
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
							throw new InvalidStateException($string);
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
						throw new InvalidStateException($string);
					}

					$property = $metaData->addProperty($property, $type, $mode, $class);
					self::$property = $property;
					$string = preg_replace_callback('#\{\s*([^\s\}]+)(?:\s+([^\}]*))?\s*\}#si', array(__CLASS__, 'callOnProperty'), $string);
					self::$property = NULL;

					if (preg_match('#\{|\}#',$string)) throw new Exception($string);

				}
			}

			if (isset($annotations['fk']) OR isset($annotations['foreignKey']))
			{
				throw new DeprecatedException("Annotation @fk and @foreignKey is deprecated use {1:1 repo} instead; in {$class}.");
			}
		}
		return $metaData;
	}

	/**
	 * callback
	 * Vola metodu na property. To je cokoli v kudrnatych zavorkach.
	 * @internal
	 * @param array
	 * @see self::$property
	 */
	private static function callOnProperty($match)
	{
		$property = self::$property;

		$name = strtolower($match[1]);
		if (isset(self::$aliases[$name])) $name = self::$aliases[$name];
		$method = "set{$name}";
		if (!method_exists($property, $method)) throw new Exception($name);
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

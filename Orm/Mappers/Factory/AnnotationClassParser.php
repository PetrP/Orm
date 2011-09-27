<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use ReflectionClass;
use stdClass;

/**
 * Loads class name in annotations.
 *
 * Annotation is only for some interface.
 * It support inheritance.
 *
 * <code>
 * $parser = new AnnotationClassParser;
 * $parser->register('foo', 'FooInterface');
 *
 * /** @foo BarClass * /
 * class FooClass implements FooInterface {}
 *
 * $parser->get('foo', new FooClass); // returns BarClass
 * </code>
 * @author Petr Procházka
 * @package Orm
 * @subpackage Mappers\Factory
 */
class AnnotationClassParser extends Object
{
	/** @var AnnotationsParser */
	private $parser;

	/** @var array of name => stdClass */
	private $registered = array();

	/** @param AnnotationsParser */
	public function __construct(AnnotationsParser $parser)
	{
		$this->parser = $parser;
	}

	/**
	 * <code>
	 * 	$p->register('mapper', 'Orm\IRepository', function ($repositoryClass) {
	 * 		return $repositoryClass . 'Mapper';
	 * 	});
	 * </code>
	 *
	 * @param string
	 * @param string interface name
	 * @param Callback|Closure|NULL
	 * @return AnnotationClassParser
	 * @throws AnnotationClassParserException
	 */
	public function register($annotation, $interface, $defaultClassFallback = NULL)
	{
		if (isset($this->registered[$annotation]))
		{
			throw new AnnotationClassParserException("Parser '$annotation' is already registered");
		}
		if (!interface_exists($interface))
		{
			throw new AnnotationClassParserException("'$interface' is not valid interface");
		}
		if ($defaultClassFallback !== NULL AND !is_callable($defaultClassFallback) AND !Callback::is($defaultClassFallback))
		{
			$tmp = is_string($defaultClassFallback) ? $defaultClassFallback : (is_object($defaultClassFallback) ? get_class($defaultClassFallback) : gettype($defaultClassFallback));
			throw new AnnotationClassParserException("'$tmp' is not valid callback");
		}
		$tmp = (object) array(
			'annotation' => $annotation,
			'interface' => $interface,
			'defaultClassFallback' => $defaultClassFallback,
			'cache' => array(),
		);
		$this->registered[$annotation] = $tmp;
		return $this;
	}

	/**
	 * Get class name for object.
	 * @param string
	 * @param object
	 * @return string class name
	 * @throws AnnotationClassParserException
	 * @throws AnnotationClassParserNoClassFoundException
	 * @throws AnnotationClassParserMorePossibleClassesException
	 */
	public function get($annotation, $object)
	{
		if (!isset($this->registered[$annotation]))
		{
			throw new AnnotationClassParserException("parser '$annotation' is not registered");
		}
		if (!is_object($object))
		{
			$tmp = gettype($object);
			throw new AnnotationClassParserException("expected object, $tmp given");
		}
		$r = $this->registered[$annotation];
		if (!($object instanceof $r->interface))
		{
			$tmp = get_class($object);
			throw new AnnotationClassParserException("'$tmp' is not instance of {$r->interface}");
		}

		$class = get_class($object);

		if (!isset($r->cache[$class]))
		{
			$result = $this->getByReflection(
				$r,
				new ReflectionClass($class),
				$this->defaultClassFallback($r, $class)
			);
			if (!$result)
			{
				throw new AnnotationClassParserNoClassFoundException("$class::@$annotation no class found");
			}
			$r->cache[$class] = $result;
		}
		return $r->cache[$class];
	}

	/**
	 * @param stdClass
	 * @param string
	 * @return string|NULL
	 */
	private function defaultClassFallback(stdClass $r, $class)
	{
		if ($r->defaultClassFallback)
		{
			$defaultClass = call_user_func($r->defaultClassFallback, $class);
			if (class_exists($defaultClass))
			{
				return $defaultClass;
			}
		}
		return NULL;
	}

	/**
	 * @param stdClass
	 * @param string|false
	 * @return string|false
	 */
	private function getByClassName(stdClass $r, $class)
	{
		if (!$class)
		{
			return NULL;
		}
		if (!isset($r->cache[$class]))
		{
			$r->cache[$class] = false;
			$reflection = new ReflectionClass($class);
			$defaultClass = NULL;
			if ($reflection AND $reflection->implementsInterface($r->interface))
			{
				if ($reflection->isInstantiable())
				{
					if ($dc = $this->defaultClassFallback($r, $class))
					{
						$dcReflection = new ReflectionClass($dc);
						if ($dcReflection->isInstantiable())
						{
							$defaultClass = $dcReflection->getName();
						}
					}
				}
				$r->cache[$class] = $this->getByReflection($r, $reflection, $defaultClass);
			}
		}
		return $r->cache[$class];
	}

	/**
	 * @param stdClass
	 * @param string
	 * @return string|false
	 * @throws AnnotationClassParserException
	 * @throws AnnotationClassParserMorePossibleClassesException
	 */
	private function getByReflection(stdClass $r, ReflectionClass $reflection, $defaultClass)
	{
		$annotation = $this->parser->getByReflection($reflection);
		if (isset($annotation[$r->annotation]))
		{
			if (count($annotation[$r->annotation]) !== 1)
			{
				throw new AnnotationClassParserException('Cannot redeclare ' . $reflection->getName() . '::@' . $r->annotation);
			}
			$class = $annotation[$r->annotation][0];
			if ($class === false)
			{
				$defaultClass = NULL;
			}
			else
			{
				if (!is_string($class))
				{
					$tmp = gettype($class);
					throw new AnnotationClassParserException($reflection->getName() . "::@{$r->annotation} expected class name, $tmp given");
				}
				if (PHP_VERSION_ID >= 50300 AND ($ns = $reflection->getNamespaceName()) !== '' AND class_exists($ns . '\\' . $class))
				{
					$class = $ns . '\\' . $class;
				}
				else if (!class_exists($class))
				{
					throw new AnnotationClassParserException($reflection->getName() . "::@{$r->annotation} class '$class' not exists");
				}
				if ($defaultClass AND strcasecmp($class, $defaultClass) !== 0)
				{
					throw new AnnotationClassParserMorePossibleClassesException('Exists annotation ' . $reflection->getName() . '::@' . $r->annotation . " and fallback '$defaultClass'");
				}
				return $class;
			}
		}
		if ($defaultClass)
		{
			return $defaultClass;
		}

		return $this->getByClassName($r, get_parent_class($reflection->getName()));
	}

	/** @deprecated */
	final protected function getAnnotations(ReflectionClass $reflection)
	{
		throw new DeprecatedException(array(__CLASS__, 'getAnnotations()', '', __CLASS__ . '->parser->getByReflection()'));
	}

}

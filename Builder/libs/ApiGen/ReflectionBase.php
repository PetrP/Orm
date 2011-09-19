<?php

/**
 * ApiGen 2.1.0 - API documentation generator for PHP 5.3+
 *
 * Copyright (c) 2010 David Grudl (http://davidgrudl.com)
 * Copyright (c) 2011 Jaroslav Hanslík (https://github.com/kukulich)
 * Copyright (c) 2011 Ondřej Nešpor (https://github.com/Andrewsville)
 *
 * For the full copyright and license information, please view
 * the file LICENSE that was distributed with this source code.
 */

namespace ApiGen;

use TokenReflection\IReflection;
use TokenReflection\ReflectionAnnotation;

/**
 * Base reflection envelope.
 *
 * Alters TokenReflection\IReflection functionality for ApiGen.
 *
 * @author Jaroslav Hanslík
 * @author Ondřej Nešpor
 */
abstract class ReflectionBase
{
	/**
	 * List of classes.
	 *
	 * @var \ArrayObject
	 */
	protected $classes;

	/**
	 * List of functions.
	 *
	 * @var \ArrayObject
	 */
	protected $functions;

	/**
	 * Generator.
	 *
	 * @var \ApiGen\Generator
	 */
	protected $generator = null;

	/**
	 * Config.
	 *
	 * @var \ApiGen\Config
	 */
	protected $config = null;

	/**
	 * Class methods cache.
	 *
	 * @var array
	 */
	private static $reflectionMethods = array();

	/**
	 * Reflection type (reflection class).
	 *
	 * @var string
	 */
	private $reflectionType;

	/**
	 * Inspected class reflection.
	 *
	 * @var \TokenReflection\IReflectionClass
	 */
	protected $reflection;

	/**
	 * Cache for information if the class should be documented.
	 *
	 * @var boolean
	 */
	protected $isDocumented;

	/**
	 * Constructor.
	 *
	 * Sets the inspected element reflection.
	 *
	 * @param \TokenReflection\IReflection $reflection Inspected element reflection
	 * @param \ApiGen\Generator $generator ApiGen generator
	 */
	public function __construct(IReflection $reflection, Generator $generator)
	{
		$this->generator = $generator;
		$this->config = $generator->getConfig();
		$this->classes = $generator->getClasses();
		$this->functions = $generator->getFunctions();

		$this->reflectionType = get_class($this);
		if (!isset(self::$reflectionMethods[$this->reflectionType])) {
			self::$reflectionMethods[$this->reflectionType] = array_flip(get_class_methods($this));
		}

		$this->reflection = $reflection;
	}

	/**
	 * Retrieves a property or method value.
	 *
	 * First tries the envelope object's property storage, then its methods
	 * and finally the inspected element reflection.
	 *
	 * @param string $name Attribute name
	 * @return mixed
	 */
	public function __get($name)
	{
		$key = ucfirst($name);
		if (isset(self::$reflectionMethods[$this->reflectionType]['get' . $key])) {
			return $this->{'get' . $key}();
		}

		if (isset(self::$reflectionMethods[$this->reflectionType]['is' . $key])) {
			return $this->{'is' . $key}();
		}

		return $this->reflection->__get($name);
	}

	/**
	 * Checks if the given property exists.
	 *
	 * First tries the envelope object's property storage, then its methods
	 * and finally the inspected element reflection.
	 *
	 * @param mixed $name Property name
	 * @return boolean
	 */
	public function __isset($name)
	{
		$key = ucfirst($name);
		return isset(self::$reflectionMethods[$this->reflectionType]['get' . $key]) || isset(self::$reflectionMethods[$this->reflectionType]['is' . $key]) || $this->reflection->__isset($name);
	}

	/**
	 * Calls a method of the inspected element reflection.
	 *
	 * @param string $name Method name
	 * @param array $args Arguments
	 * @return mixed
	 */
	public function __call($name, $args)
	{
		return call_user_func_array(array($this->reflection, $name), $args);
	}

	/**
	 * Returns if the class should be documented.
	 *
	 * @return boolean
	 */
	public function isMain()
	{
		return empty($this->config->main) || 0 === strpos($this->reflection->getName(), $this->config->main);
	}

	/**
	 * Returns if the element should be documented.
	 *
	 * @return boolean
	 */
	public function isDocumented()
	{
		if (null === $this->isDocumented) {
			$this->isDocumented = $this->reflection->isTokenized() || $this->reflection->isInternal();

			if ($this->isDocumented) {
				if (!$this->config->php && $this->reflection->isInternal()) {
					$this->isDocumented = false;
				} elseif (!$this->config->deprecated && $this->reflection->isDeprecated()) {
					$this->isDocumented = false;
				} elseif (!$this->config->internal && ($internal = $this->reflection->getAnnotation('internal')) && empty($internal[0])) {
					$this->isDocumented = false;
				}
			}
		}

		return $this->isDocumented;
	}

	/**
	 * Returns element package name (including subpackage name).
	 *
	 * For internal elements returns "PHP", for elements in global space returns "None".
	 *
	 * @return string
	 */
	public function getPseudoPackageName()
	{
		if ($this->reflection->isInternal()) {
			return 'PHP';
		}

		if ($package = $this->reflection->getAnnotation('package')) {
			$packageName = preg_replace('~\s+.*~s', '', $package[0]);
			if ($subpackage = $this->reflection->getAnnotation('subpackage')) {
				$packageName .= '\\' . preg_replace('~\s+.*~s', '', $subpackage[0]);
			}
			return $packageName;
		}

		return 'None';
	}

	/**
	 * Returns element namespace name.
	 *
	 * For internal elements returns "PHP", for elements in global space returns "None".
	 *
	 * @return string
	 */
	public function getPseudoNamespaceName()
	{
		return $this->reflection->isInternal() ? 'PHP' : $this->reflection->getNamespaceName() ?: 'None';
	}

	/**
	 * Returns a particular annotation value.
	 *
	 * @param string $name Annotation name
	 * @return string|array|null
	 */
	final public function getAnnotation($name)
	{
		if ($name === ReflectionAnnotation::SHORT_DESCRIPTION)
		{
			$desc = $this->reflection->getAnnotation(ReflectionAnnotation::SHORT_DESCRIPTION);
			if (!$desc AND ($this instanceof ReflectionConstant OR $this instanceof ReflectionProperty OR $this instanceof ReflectionParameter))
			{
				$tmp = $this->reflection->getAnnotation('var');
				if (is_array($tmp) AND isset($tmp[0]) AND !isset($tmp[1]))
				{
					$tmp = preg_split('#\s#', $tmp[0], 2);
					$desc = (isset($tmp[1]) AND $tmp[1]) ? $tmp[1] : NULL;
				}
			}
			return $desc;
		}
		return $this->reflection->getAnnotation($name);
	}

	/**
	 * Returns all annotations.
	 *
	 * @return array
	 */
	final public function getAnnotations()
	{
		$tmp = $this->reflection->getAnnotations();
		$tmp[ReflectionAnnotation::SHORT_DESCRIPTION] = $this->getAnnotation(ReflectionAnnotation::SHORT_DESCRIPTION);
		if (!isset($tmp[ReflectionAnnotation::SHORT_DESCRIPTION]))
		{
			unset($tmp[ReflectionAnnotation::SHORT_DESCRIPTION]);
		}
		return $tmp;
	}
}

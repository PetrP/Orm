<?php

namespace Orm;

use Nette\Object;
use Nette\InvalidStateException;
use Nette\InvalidArgumentException;
use Exception;
use ReflectionMethod;

require_once dirname(__FILE__) . '/IEntityInjection.php';
require_once dirname(__FILE__) . '/IEntityInjectionStaticLoader.php';

abstract class Injection extends Object implements IEntityInjection, IEntityInjectionStaticLoader
{
	protected $value;

	public static function create($className, IEntity $entity, $value = NULL)
	{
		if (method_exists($className, '__construct'))
		{
			$construct = new ReflectionMethod($className, '__construct');
			if ($construct->getNumberOfRequiredParameters())
			{
				throw new InvalidStateException("$className has required parameters in constructor, use custom factory");
			}
		}
		$injection = new $className;
		if (!($injection instanceof self))
		{
			throw new InvalidArgumentException(get_class($injection) . " is't subclass of " . __CLASS__);
		}
		return $injection;
	}

	public function getInjectedValue()
	{
		return $this->value;
	}

	public function setInjectedValue($value)
	{
		$this->value = $value;
	}
}

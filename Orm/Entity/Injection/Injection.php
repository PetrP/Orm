<?php

namespace Orm;

use Nette\Object;
use Nette\InvalidStateException;
use Nette\InvalidArgumentException;
use Exception;
use ReflectionMethod;

require_once __DIR__ . '/IEntityInjection.php';
require_once __DIR__ . '/IEntityInjectionStaticLoader.php';

/**
 * Obecna implementace IEntityInjection
 * @see IEntityInjection
 */
abstract class Injection extends Object implements IEntityInjection, IEntityInjectionStaticLoader
{

	/** @var mixed */
	protected $value;

	/**
	 * @param string
	 * @param IEntity
	 * @param mixed
	 * @return IEntityInjection
	 */
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

	/**
	 * Hodnota ktera se bude ukladat do uloziste.
	 * @return mixed
	 */
	public function getInjectedValue()
	{
		return $this->value;
	}

	/**
	 * To co prijde od uzivatele.
	 * @param mixed
	 */
	public function setInjectedValue($value)
	{
		$this->value = $value;
		return $this;
	}

}

<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use Exception;
use ReflectionMethod;

/**
 * Base implementation of IEntityInjection
 * @see IEntityInjection
 * @author Petr Procházka
 * @package Orm
 * @subpackage Entity\Injection
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
	public static function create($className, IEntity $entity, $value)
	{
		if (method_exists($className, '__construct'))
		{
			$construct = new ReflectionMethod($className, '__construct');
			if ($construct->getNumberOfRequiredParameters())
			{
				throw new RequiredArgumentException(get_class($entity) . " injection '$className' loaded via " . __CLASS__ . '::create() has required parameters in constructor, use custom factory.');
			}
		}
		$injection = new $className;
		if (!($injection instanceof self))
		{
			throw new InvalidArgumentException(array('', get_class($entity) . ' injection loaded via ' . __CLASS__ . '::create()', 'subclass of ' . __CLASS__, $className));
		}

		$injection->setInjectedValue($value);

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
	 * To co prijde od uzivatele nebo z uloziste.
	 * @param mixed
	 * @return Injection $this
	 */
	public function setInjectedValue($value)
	{
		$this->value = $value;
		return $this;
	}

}

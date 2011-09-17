<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use ArrayIterator;
use ReflectionMethod;
use ReflectionProperty;

/**
 * Base entity things.
 *
 * Sets multiple properties (setValues).
 * Convert entity to array (toArray).
 * Possibility to iterate (IteratorAggregate).
 * Access to properties like to array (ArrayAccess).
 * Loads MetaData - information about properties (createMetaData). Default from annotation {@see AnnotationMetaData}
 *
 * @see Entity
 * @author Petr Procházka
 * @package Orm
 * @subpackage Entity\EntityFragments
 */
abstract class BaseEntityFragment extends ValueEntityFragment
{

	/**
	 * Entity prevadi na array, je mozne nastavit co udelat z asociacemi.
	 * @see EntityToArray
	 * @param int EntityToArray::*
	 * @return array
	 */
	final public function toArray($mode = EntityToArray::AS_IS)
	{
		return EntityToArray::toArray($this, $mode);
	}

	/**
	 * Nastavuje parametry.
	 * Kdyz neexistuje parametr:
	 * vola setter `set<Param>` kdyz existuje takova methoda a je public;
	 * plni property `$param` kdyz existuje a je public.
	 * @param array|Traversable $values
	 * @return Entity $this
	 */
	final public function setValues($values)
	{
		foreach ($values as $name => $value)
		{
			if ($this->hasParam($name, MetaData::WRITE))
			{
				$this->__set($name, $value);
			}
			else if (method_exists($this, "set$name"))
			{
				$r = new ReflectionMethod($this, "set$name");
				if ($r->isPublic())
				{
					$r->invoke($this, $value);
				}
			}
			else if (property_exists($this, $name))
			{
				$r = new ReflectionProperty($this, $name);
				if ($r->isPublic())
				{
					$r->setValue($this, $value);
				}
			}
		}

		return $this;
	}

	/**
	 * Vytvori MetaData
	 * @param string|IEntity class name or object
	 * @return MetaData
	 */
	public static function createMetaData($entityClass)
	{
		return AnnotationMetaData::getMetaData($entityClass);
	}

	/**
	 * @return ArrayIterator
	 * @see IteratorAggregate
	 */
	final public function getIterator()
	{
		return new ArrayIterator($this->toArray());
	}

	/**
	 * @param string
	 * @return bool
	 * @see ArrayAccess
	 */
	final public function offsetExists($name)
	{
		return $this->__isset($name);
	}

	/**
	 * @param string
	 * @return mixed
	 * @see ArrayAccess
	 */
	final public function offsetGet($name)
	{
		return $this->__get($name);
	}

	/**
	 * @param string
	 * @param mixed
	 * @return Entity $this
	 * @see ArrayAccess
	 */
	final public function offsetSet($name, $value)
	{
		return $this->__set($name, $value);
	}

	/**
	 * @param string
	 * @throws NotSupportedException
	 * @see ArrayAccess
	 */
	final public function offsetUnset($name)
	{
		throw new NotSupportedException();
	}

}

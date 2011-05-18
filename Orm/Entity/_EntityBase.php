<?php

namespace Orm;

use ArrayIterator;
use Nette\NotSupportedException;
use Nette\DeprecatedException;

/**
 * Hromadne nastavovani dat (setValues), prevod na pole (toArray),
 * moznost iterovat (IteratorAggregate) a pristupovat k parametrum jako k poli (ArrayAccess)
 *
 * Vytvareni MetaData, tedy informaci o parametrech entit (createMetaData).
 * Defaultne se nacita z anotaci.
 * @see AnnotationMetaData
 *
 * @see Entity
 */
abstract class _EntityBase extends _EntityValue
{

	/**
	 * Entity prevadi na array, je monze nastavit co udelat z asociacemi.
	 * @see EntityToArray
	 * @param int EntityToArray::*
	 * @return array
	 */
	final public function toArray($mode = EntityToArray::AS_IS)
	{
		return EntityToArray::toArray($this, MetaData::getEntityRules(get_class($this)), $mode);
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
				$r = $this->getReflection()->getMethod("set$name");
				if ($r->isPublic())
				{
					$r->invoke($this, $value);
				}
			}
			else if (property_exists($this, $name))
			{
				$r = $this->getReflection()->getProperty($name);
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
	 * @throws NotSupportedException
	 * @see ArrayAccess
	 */
	final public function offsetUnset($name)
	{
		throw new NotSupportedException();
	}



	/** @deprecated @see EntityToArray::AS_ID */
	const ENTITY_TO_ID = 'deprecated';
	/** @deprecated @see EntityToArray::AS_ARRAY */
	const ENTITY_TO_ARRAY = 'deprecated';
	/** @deprecated @see NULL */
	const EXISTS = 'deprecated';
	/** @deprecated @see MetaData::READ */
	const READ = 'deprecated';
	/** @deprecated @see MetaData::WRITE */
	const WRITE = 'deprecated';
	/** @deprecated @see MetaData::READWRITE */
	const READWRITE = 'deprecated';
	/** @deprecated */
	final protected function check(){throw new DeprecatedException('Use Orm\Entity::onBeforePersist() instead');}
	/** @deprecated */
	final public function toPlainArray(){throw new DeprecatedException('Use Orm\Entity::toArray(Orm\EntityToArray::AS_ID) instead');}
	/** @deprecated */
	final protected static function createEntityRules($entityClass){throw new DeprecatedException('Use Orm\Entity::createMetaData instead');}
}

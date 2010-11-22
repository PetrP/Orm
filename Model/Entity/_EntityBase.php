<?php

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



	/** @ignore @deprecated */
	const ENTITY_TO_ID = EntityToArray::AS_ID;
	/** @ignore @deprecated */
	const ENTITY_TO_ARRAY = EntityToArray::AS_ARRAY;

	/** @ignore @deprecated */
	const EXISTS = NULL;
	/** @ignore @deprecated */
	const READ = MetaData::READ;
	/** @ignore @deprecated */
	const WRITE = MetaData::WRITE;
	/** @ignore @deprecated */
	const READWRITE = MetaData::READWRITE;
	/** @ignore @deprecated */
	final protected function check(){}
	/** @ignore @deprecated */
	final public function toPlainArray()
	{
		return $this->toArray(EntityToArray::AS_ID);
	}
	/** @ignore @deprecated */
	final protected static function createEntityRules($entityClass)
	{
	 	return call_user_func(array($entityClass, 'createMetaData'));
	}

}

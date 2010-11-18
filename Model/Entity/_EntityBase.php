<?php

/**
 * Hromadne nastavovani dat (setValues), prevod na pole (toArray),
 * moznost iterovat (IteratorAggregate) a pristupovat k parametrum jako k poli (ArrayAccess)
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



	/**
	 * @todo zrusit
	 * @internal
	 * @param string
	 * @return array
	 */
	final public static function ___getFk($entityName)
	{
		$result = array();
		foreach (MetaData::getEntityRules($entityName) as $name => $rule)
		{
			if ($rule['relationship'] !== MetaData::ManyToOne AND $rule['relationship'] !== MetaData::OneToOne) continue;
			$result[$name] = $rule['relationshipParam'];
		}
		return $result;
	}


	/** @deprecated */
	const ENTITY_TO_ID = EntityToArray::AS_ID;
	/** @deprecated */
	const ENTITY_TO_ARRAY = EntityToArray::AS_ARRAY;

	/** @deprecated */
	const EXISTS = NULL;
	/** @deprecated */
	const READ = MetaData::READ;
	/** @deprecated */
	const WRITE = MetaData::WRITE;
	/** @deprecated */
	const READWRITE = MetaData::READWRITE;

	/** @deprecated */
	final protected function check(){}
	/** @deprecated */
	final public function toPlainArray()
	{
		return $this->toArray(EntityToArray::AS_ID);
	}

}

<?php
abstract class _EntityBase extends _EntityValue
{
	const ENTITY_TO_ID = EntityToArray::AS_ID; // deprecated
	const ENTITY_TO_ARRAY = EntityToArray::AS_ARRAY; // deprecated

	final public function toArray($mode = EntityToArray::AS_IS)
	{
		return EntityToArray::toArray($this, self::getEntityRules(get_class($this)), $mode);
	}

	final public function setValues($values)
	{
		foreach ($values as $name => $value)
		{
			if ($this->hasParam($name, self::WRITE))
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
	}

	final public function getIterator()
	{
		return new ArrayIterator($this->toArray());
	}

	final public function offsetExists($name)
	{
		return $this->__isset($name);
	}
	final public function offsetGet($name)
	{
		return $this->__get($name);
	}
	final public function offsetSet($name, $value)
	{
		return $this->__set($name, $value);
	}
	final public function offsetUnset($name)
	{
		throw new NotSupportedException();
	}



	/**
	 * @internal
	 */
	final public static function ___getFk($entityName)
	{
		$result = array();
		foreach (Entity::getEntityRules($entityName) as $name => $rule)
		{
			if ($rule['relationship'] !== MetaData::ManyToOne AND $rule['relationship'] !== MetaData::OneToOne) continue;
			$result[$name] = $rule['relationshipParam'];
		}
		return $result;
	}



	/** @deprecated */
	final protected function check(){}
	/** @deprecated */
	final public function toPlainArray()
	{
		return $this->toArray(self::ENTITY_TO_ID);
	}

}

<?php

abstract class Entity2 extends Entity3 implements ArrayAccess, IteratorAggregate
{

	final public function setValues($values)
	{
		foreach ($values as $name => $value)
		{
			// todo nepokusit se zapsat i nezname?
			if ($this->hasParam($name, self::WRITE))
			{
				$this->__set($name, $value);
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
	
}
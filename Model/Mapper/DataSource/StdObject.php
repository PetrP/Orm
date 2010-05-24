<?php

class StdObject extends stdClass implements ArrayAccess
{
	public function __construct(array $arr)
	{
		foreach ($arr as $k => $v) $this->$k = $v;
	}

	public function toArray()
	{
		return (array) $this;
	}

	public function offsetExists($key)
	{
		return isset($this->{$key});
	}
	public function offsetGet($key)
	{
		return $this->{$key};
	}
	public function offsetSet($key, $value)
	{
		$this->{$key} = $value;
	}
	public function offsetUnset($key)
	{
		unset($this->{$key});
	}
}

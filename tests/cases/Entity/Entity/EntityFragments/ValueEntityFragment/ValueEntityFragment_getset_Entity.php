<?php

/**
 * @property-read $readOnly
 * @property TestEntity $fk {1:1 TestEntity}
 * @property TestEntity|NULL $fk2 {m:1 TestEntity}
 */
class ValueEntityFragment_getset_Entity extends TestEntity
{
	private $method;
	public function getMethod()
	{
		return $this->method;
	}
	public function setMethod($v)
	{
		$this->method = $v;
	}

	public function gv($name/*, $need*/)
	{
		if (func_num_args() > 1)
		{
			return $this->getValue($name, func_get_arg(2));
		}
		return $this->getValue($name);
	}

	public function sv($name, $value)
	{
		return $this->setValue($name, $value);
	}

	public function srov($name, $value)
	{
		return $this->setReadOnlyValue($name, $value);
	}
}

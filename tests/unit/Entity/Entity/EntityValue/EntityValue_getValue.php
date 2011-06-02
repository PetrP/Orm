<?php

/**
 * @property int $foo
 * @property int $foo2
 */
class EntityValue_getValue_Entity extends TestEntity
{
	public function setFoo($foo)
	{
		parent::setFoo($foo);
		throw new Exception;
	}

	public function __getValue($name, $need)
	{
		return $this->getValue($name, $need);
	}
}

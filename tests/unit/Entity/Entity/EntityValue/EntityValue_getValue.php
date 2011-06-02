<?php

use Orm\Repository;
use Orm\IEntity;

/**
 * @property int $foo
 * @property int $foo2
 * @property $mixed
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

class EntityValue_getValue_LazyRepository extends Repository
{
	public function lazyLoad(IEntity $entity, $param)
	{
		return array(
			$param => 'lazy',
			'foo' => 5,
			'foo2' => 3,
			'unexists' => 'unexists',
		);
	}
}

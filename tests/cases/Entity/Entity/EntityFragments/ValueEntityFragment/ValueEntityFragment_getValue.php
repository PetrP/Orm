<?php

use Orm\Repository;
use Orm\IEntity;

/**
 * @property int $foo {default 0}
 * @property int $foo2
 * @property int $foo3
 * @property $mixed
 */
class ValueEntityFragment_getValue_Entity extends TestEntity
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

class ValueEntityFragment_getValue_LazyRepository extends Repository
{
	protected $entityClassName = 'ValueEntityFragment_getValue_Entity';
	public $count = 0;
	public function lazyLoad(IEntity $entity, $param)
	{
		$this->count++;
		return array(
			$param => 'lazy',
			'foo3' => 5,
			'foo2' => 3,
			'unexists' => 'unexists',
		);
	}
}

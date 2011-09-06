<?php

use Orm\Entity;
use Orm\Repository;
use Orm\ArrayMapper;
use Orm\ManyToMany;
use Orm\ArrayManyToManyMapper;
use Orm\IRepository;
use Orm\IEntity;
use Nette\Utils\Html;

/**
 * @property EntityValue_injectionEntity_ManyToMany $many {m:m EntityValue_injection}
 */
class EntityValue_injectionEntity extends Entity
{

}

class EntityValue_injectionRepository extends Repository
{
	protected $entityClassName = 'EntityValue_injectionEntity';
}

class EntityValue_injectionMapper extends ArrayMapper
{
	protected function loadData()
	{
		return array(
			'1' => array('id' => 1),
			'2' => array('id' => 2),
			'3' => array('id' => 3),
			'4' => array('id' => 4),
		);
	}

	public function createManyToManyMapper($firstParam, IRepository $repository, $secondParam)
	{
		return new EntityValue_injection_ManyToManyMapper;
	}
}

class EntityValue_injectionEntity_ManyToMany extends ManyToMany
{

	public function getMapper()
	{
		return parent::getMapper();
	}

	public $create = 0;
	public function __construct(IEntity $parent, $repository, $childParam, $parentParam, $mapped, $value = NULL)
	{
		parent::__construct($parent, $repository, $childParam, $parentParam, $mapped, $value);
		$this->create++;
	}

	public $setInjectedValue = 0;
	public function setInjectedValue($value)
	{
		$this->{__FUNCTION__}++;
		return parent::setInjectedValue($value);
	}

	public $getInjectedValue = 0;
	public function getInjectedValue()
	{
		$this->{__FUNCTION__}++;
		return parent::getInjectedValue();
	}
}

class EntityValue_injection_ManyToManyMapper extends ArrayManyToManyMapper
{
	public $setValue = 0;
	public function setInjectedValue($value)
	{
		$this->setValue++;
		return parent::setInjectedValue($value);
	}
}

/**
 * @property Orm\ManyToMany $i {injection self::createInjection()}
 */
class EntityValue_injectionBadEntity extends Entity
{
	public static function createInjection()
	{
		return new Nette\Utils\Html;
	}
}

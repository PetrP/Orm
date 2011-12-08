<?php

use Orm\Entity;
use Orm\Repository;
use Orm\ArrayMapper;
use Orm\ManyToMany;
use Orm\ArrayManyToManyMapper;
use Orm\IRepository;
use Orm\IEntity;
use Orm\RelationshipMetaDataManyToMany;

/**
 * @property ValueEntityFragment_injectionEntity_ManyToMany $many {m:m ValueEntityFragment_injection}
 */
class ValueEntityFragment_injectionEntity extends Entity
{

}

class ValueEntityFragment_injectionRepository extends Repository
{
	protected $entityClassName = 'ValueEntityFragment_injectionEntity';
}

class ValueEntityFragment_injectionMapper extends ArrayMapper
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
		return new ValueEntityFragment_injection_ManyToManyMapper;
	}
}

class ValueEntityFragment_injectionEntity_ManyToMany extends ManyToMany
{

	public function getMapper()
	{
		return parent::getMapper();
	}

	public $create = 0;
	public function __construct(IEntity $parent, RelationshipMetaDataManyToMany $metaData, $value = NULL)
	{
		parent::__construct($parent, $metaData, $value);
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

class ValueEntityFragment_injection_ManyToManyMapper extends ArrayManyToManyMapper
{
	public $setValue = 0;
	public function validateInjectedValue($value)
	{
		$this->setValue++;
		return parent::validateInjectedValue($value);
	}
}

/**
 * @property Orm\ManyToMany $i {injection self::createInjection()}
 */
class ValueEntityFragment_injectionBadEntity extends Entity
{
	public static function createInjection()
	{
		return new Directory;
	}
}

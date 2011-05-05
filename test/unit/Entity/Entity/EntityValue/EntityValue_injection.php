<?php

/**
 * @property EntityValue_injectionToEntityValue_injection $many {m:m}
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
}

class EntityValue_injectionToEntityValue_injection extends OldManyToMany
{
	protected function createMapper(IRepository $firstRepository, IRepository $secondRepository)
	{
		return new EntityValue_injection_ManyToManyMapper;
	}

	public function getMapper()
	{
		return parent::getMapper();
	}

	public $create = 0;
	public function __construct(IEntity $entity, $repository, $param, $value = NULL)
	{
		parent::__construct($entity, $repository, $param, $value);
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
	public function setValue($value)
	{
		$this->{__FUNCTION__}++;
		return parent::setValue($value);
	}
}

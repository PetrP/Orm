<?php

use Orm\Injection;
use Orm\IEntityInjection;
use Orm\IEntityInjectionLoader;
use Orm\IEntity;

class MetaDataProperty_setInjection_Injection extends Injection
{

}

class MetaDataProperty_setInjection_JustInjection implements IEntityInjection
{
	function getInjectedValue(){}
	function setInjectedValue($value){}
}

class MetaDataProperty_setInjection_NonStaticInjectionLoader implements IEntityInjectionLoader
{
	function create($className, IEntity $entity, $value){}
}

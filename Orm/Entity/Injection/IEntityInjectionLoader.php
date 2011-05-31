<?php

namespace Orm;

interface IEntityInjectionLoader
{
	function create($className, IEntity $entity, $value = NULL);
}

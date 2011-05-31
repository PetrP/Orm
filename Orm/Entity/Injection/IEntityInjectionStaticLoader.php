<?php

namespace Orm;

interface IEntityInjectionStaticLoader
{
	static function create($className, IEntity $entity, $value = NULL);
}

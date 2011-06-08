<?php

namespace Orm;

/**
 * Injection loader.
 * @see MetaDataProperty::setInjection
 */
interface IEntityInjectionLoader
{

	/**
	 * @param string
	 * @param IEntity
	 * @param mixed
	 * @return IEntityInjection
	 */
	function create($className, IEntity $entity, $value = NULL);

}

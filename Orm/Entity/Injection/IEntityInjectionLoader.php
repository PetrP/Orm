<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

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
	function create($className, IEntity $entity, $value);

}

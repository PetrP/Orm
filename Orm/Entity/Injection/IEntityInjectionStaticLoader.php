<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * Static injection loader.
 * @see MetaDataProperty::setInjection
 * @author Petr Procházka
 * @package Orm
 * @subpackage Entity\Injection
 */
interface IEntityInjectionStaticLoader
{

	/**
	 * @param string
	 * @param IEntity
	 * @param mixed
	 * @return IEntityInjection
	 */
	static function create($className, IEntity $entity, $value);

}

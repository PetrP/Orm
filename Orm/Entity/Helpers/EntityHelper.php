<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * Helper for entity.
 * @author Petr Procházka
 * @package Orm
 * @subpackage Entity\Helpers
 */
class EntityHelper
{

	/**
	 * @param IEntity
	 * @return string
	 */
	public static function toString(IEntity $entity)
	{
		$string = get_class($entity);
		if ($entity->hasParam('id') AND isset($entity->id))
		{
			$string .= '#' . $entity->id;
		}
		return $string;
	}

}

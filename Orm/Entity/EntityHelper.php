<?php

namespace Orm;

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

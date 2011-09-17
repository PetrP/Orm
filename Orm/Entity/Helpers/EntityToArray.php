<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * Convert entity to array. It's possible set what do with asscoations.
 * @see Entity::toArray()
 * @author Petr Procházka
 * @package Orm
 * @subpackage Entity\Helpers
 */
class EntityToArray extends Object
{
	/**#@+ @var int nastaveni prevodu */

	/** Vse se vraci tak jak je */
	const AS_IS = 9; // self::ENTITY_AS_IS | self::RELATIONSHIP_AS_IS
	/** Entity se prevedou na id (i v ManyToMany OneToMany atd.) */
	const AS_ID = 18; // self::ENTITY_AS_ID | self::RELATIONSHIP_AS_ARRAY_OF_ID
	/** Entity se prevedou na pole (i v ManyToMany OneToMany atd.) */
	const AS_ARRAY = 36; // self::ENTITY_AS_ARRAY | self::RELATIONSHIP_AS_ARRAY_OF_ARRAY

	/** Entity se vraceji tak jak jsou */
	const ENTITY_AS_IS = 1;
	/** Entity se prevedou na id */
	const ENTITY_AS_ID = 2;
	/** Entity se prevedou na pole */
	const ENTITY_AS_ARRAY = 4;

	/** ManyToMany OneToMany atd. se vraceji tak jak jsou */
	const RELATIONSHIP_AS_IS = 8;
	/** ManyToMany OneToMany atd. se prevedou na pole a jejich Entity na idcka */
	const RELATIONSHIP_AS_ARRAY_OF_ID = 16;
	/** ManyToMany OneToMany atd. se prevedou na pole a jejich Entity na pole */
	const RELATIONSHIP_AS_ARRAY_OF_ARRAY = 32;

	/**#@-*/

	/** @var int AS_ARRAY se muze zaciklit, maximalni hloupka do ktere se pole vytvari */
	public static $maxDeep = 3;

	/**
	 * @internal
	 * @param IEntity
	 * @param int
	 * @param int
	 */
	public static function toArray(IEntity $entity, $mode = self::AS_IS, $deep = 0)
	{
		if ($mode === NULL) $mode = self::AS_IS;
		$result = array(
			'id' => isset($entity->id) ? $entity->id : NULL,
		);
		$rules = MetaData::getEntityRules(get_class($entity), $entity->getModel(false));

		foreach ($rules as $name => $rule)
		{
			if ($name === 'id') continue;

			if (isset($rule['get']))
			{
				$result[$name] = $entity->__get($name);
				if ($result[$name] instanceof IEntity AND !($mode & self::ENTITY_AS_IS))
				{
					if ($mode & self::ENTITY_AS_ID)
					{
						$result[$name] = $result[$name]->id;
					}
					else if ($mode & self::ENTITY_AS_ARRAY)
					{
						$result[$name] = $deep > static::$maxDeep ? NULL : EntityToArray::toArray($result[$name], $mode, $deep+1);
					}
					else
					{
						throw new EntityToArrayNoModeException(array($entity, true, false));
					}
				}
				else if ($result[$name] instanceof IRelationship AND !($mode & self::RELATIONSHIP_AS_IS))
				{
					if ($deep > static::$maxDeep)
					{
						$result[$name] = NULL;
					}
					else
					{
						$arr = array();
						foreach ($result[$name] as $e)
						{
							if ($mode & self::RELATIONSHIP_AS_ARRAY_OF_ID)
							{
								$arr[] = $e->id;
							}
							else if ($mode & self::RELATIONSHIP_AS_ARRAY_OF_ARRAY)
							{
								$arr[] = EntityToArray::toArray($e, $mode, $deep+1);
							}
							else
							{
								throw new EntityToArrayNoModeException(array($entity, false, true));
							}
						}
						$result[$name] = $arr;
					}
				}
			}
		}

		return $result;
	}

}

<?php

/**
 * Vytvareni MetaData, tedy informaci o parametrech entit.
 * Defaultne se nacita z anotaci.
 * @see AnnotationMetaData
 * @see Entity
 */
abstract class _EntityMeta extends Object
{

	/**
	 * Vytvori MetaData
	 * @param string|IEntity class name or object
	 * @return MetaData
	 */
	protected static function createEntityRules($entityClass)
	{
		return AnnotationMetaData::getEntityParams($entityClass);
	}

	/**
	 * Vraci MetaData.
	 * Entita ma metadata jako pole pro lepsi vykon
	 * @param string class name
	 * @return array internal format
	 */
	final protected static function getEntityRules($entityClass)
	{
		static $cache = array();
		if (!isset($cache[$entityClass]))
		{
			if (!class_exists($entityClass)) throw new InvalidStateException("Class '$entityClass' doesn`t exists");
			$implements = class_implements($entityClass);
			if (!isset($implements['IEntity'])) throw new InvalidStateException("'$entityClass' isn`t instance of IEntity");
			$meta = call_user_func(array($entityClass, 'createEntityRules'), $entityClass);
			if (!($meta instanceof MetaData)) throw new InvalidStateException("It`s expected that 'IEntity::createEntityRules' will return 'MetaData'.");
			$cache[$entityClass] = $meta->toArray();
		}
		return $cache[$entityClass];
	}

}

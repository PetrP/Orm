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
	public static function createMetaData($entityClass)
	{
		return AnnotationMetaData::getMetaData($entityClass);
	}

	/** @deprecated */
	final protected static function createEntityRules($entityClass)
	{
	 	return call_user_func(array($entityClass, 'createMetaData'));
	}

}

<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * @deprecated
 * Factory for IRelationship.
 * @author Petr Procházka
 * @package Orm
 * @subpackage Relationships\MetaData
 */
final class RelationshipLoader extends Object
{

	/** Na teto strane */
	const MAPPED_HERE = RelationshipMetaDataToMany::MAPPED_HERE;

	/** Na druhe strane */
	const MAPPED_THERE = RelationshipMetaDataToMany::MAPPED_THERE;

	/** Relace ukazuje na sebe. Oba jsou stejny. Oba mapuji. */
	const MAPPED_BOTH = RelationshipMetaDataToMany::MAPPED_BOTH;

	/**
	 * @param string MetaData::ManyToMany|MetaData::OneToMany
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @param mixed RelationshipLoader::MAPPED_HERE|RelationshipLoader::MAPPED_THERE|NULL
	 */
	public function __construct($relationship, $class, $repositoryName, $param, $entityName, $parentParam, $mapped = NULL)
	{
		throw new DeprecatedException(array($this, NULL, 'Orm\RelationshipMetaDataManyToMany or Orm\RelationshipMetaDataOneToMany'));
	}

}

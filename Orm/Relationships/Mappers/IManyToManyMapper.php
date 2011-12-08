<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * Mapper for ManyToMany relationship.
 * @see IMapper::createManyToManyMapper()
 * @author Petr Procházka
 * @package Orm
 * @subpackage Relationships\Mappers
 */
interface IManyToManyMapper
{

	/** @param RelationshipMetaDataManyToMany */
	function attach(RelationshipMetaDataManyToMany $manyToMany);

	/**
	 * @param mixed {@see ManyToMany::$injectedValue}
	 * @return mixed will be set as {@see ManyToMany::$injectedValue}
	 */
	function validateInjectedValue($injectedValue);

	/**
	 * @param IEntity
	 * @param array id => id
	 * @param mixed {@see ManyToMany::$injectedValue}
	 * @return mixed will be set as {@see ManyToMany::$injectedValue}
	 */
	function add(IEntity $parent, array $ids, $injectedValue);

	/**
	 * @param IEntity
	 * @param array id => id
	 * @param mixed {@see ManyToMany::$injectedValue}
	 * @return mixed will be set as {@see ManyToMany::$injectedValue}
	 */
	function remove(IEntity $parent, array $ids, $injectedValue);

	/**
	 * @param IEntity
	 * @param mixed {@see ManyToMany::$injectedValue}
	 * @return array id => id
	 */
	function load(IEntity $parent, $injectedValue);

}

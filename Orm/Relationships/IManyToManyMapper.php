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
 */
interface IManyToManyMapper
{

	/** @param ManyToMany */
	function attach(ManyToMany $manyToMany);

	/**
	 * @param IEntity
	 * @param array id => id
	 */
	function add(IEntity $parent, array $ids);

	/**
	 * @param IEntity
	 * @param array id => id
	 */
	function remove(IEntity $parent, array $ids);

	/**
	 * @param IEntity
	 * @return array id => id
	 */
	function load(IEntity $parent);

}

<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use IteratorAggregate;
use Countable;

/**
 * Relationship
 * @author Petr Procházka
 * @package Orm
 * @subpackage Relationships
 */
interface IRelationship extends IteratorAggregate, Countable, IEntityInjection
{

	/**
	 * @param IEntity|scalar|array
	 * @return IEntity
	 */
	function add($entity);

	/**
	 * @param array of IEntity|scalar|array
	 * @return IRelationship $this
	 */
	function set(array $data);

	/**
	 * @param IEntity|scalar|array
	 * @return IEntity
	 */
	function remove($entity);

	/**
	 * @return IEntityCollection
	 */
	function get();

	/**
	 * @param IEntity|scalar|array
	 * @return bool
	 */
	function has($entity);

	/** @return void */
	function persist();

}

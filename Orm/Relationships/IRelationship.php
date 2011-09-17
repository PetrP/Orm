<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use IteratorAggregate;
use Countable;

require_once __DIR__ . '/../Entity/Injection/IEntityInjection.php';

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

	/** @return void */
	function persist();

}

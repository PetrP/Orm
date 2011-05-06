<?php

require_once dirname(__FILE__) . '/../Entity/Injection/IEntityInjection.php';

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

	function persist();

}

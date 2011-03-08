<?php

require_once dirname(__FILE__) . '/../Entity/Injection/IEntityInjection.php';

interface IRelationship extends IteratorAggregate, Countable, IEntityInjection
{

	/**
	 * @param IEntity
	 * @param string|NULL internal get_class
	 */
	function __construct(IEntity $parent, $name = NULL);

	/**
	 * @param IEntity|int|array
	 * @return IEntity
	 */
	function add($entity);

	/**
	 * @param array of IEntity|int|array
	 * @return IRelationship $this
	 */
	function set(array $data);

	/**
	 * @param IEntity|int|array
	 * @return IEntity
	 */
	function remove($entity);

	/**
	 * @return IEntityCollection
	 */
	function get();

	function persist();

}

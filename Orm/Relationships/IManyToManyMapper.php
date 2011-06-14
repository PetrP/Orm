<?php

namespace Orm;

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

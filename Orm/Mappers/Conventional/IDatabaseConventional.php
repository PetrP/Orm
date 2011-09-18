<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * Naming conventions in database.
 * Different names of keys between entity and storage.
 * Table name and primary key convention.
 * ManyToMany mapper convention.
 */
interface IDatabaseConventional extends IConventional
{

	/** @return string */
	public function getPrimaryKey();

	/**
	 * @param IRepository
	 * @return string
	 */
	public function getTable(IRepository $repository);

	/**
	 * @param IRepository
	 * @param IRepository
	 * @return string
	 */
	public function getManyToManyTable(IRepository $source, IRepository $target);

	/**
	 * @param string
	 * @return string
	 */
	public function getManyToManyParam($param);

}

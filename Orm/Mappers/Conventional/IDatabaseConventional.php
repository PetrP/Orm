<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

require_once __DIR__ . '/IConventional.php';

/** Konvence pojmenovani v databazi. */
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

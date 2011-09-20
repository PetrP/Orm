<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use Countable;
use IteratorAggregate;

/**
 * Collection of entities.
 * @author Petr Procházka
 * @package Orm
 * @subpackage Mappers\Collection
 */
interface IEntityCollection extends Countable, IteratorAggregate
{

	/** sorting order - vzestupne */
	const ASC = 'ASC';

	/** sorting order - sestupne */
	const DESC = 'DESC';

	/**
	 * Selects columns to order by.
	 * @param string|array column name or array of column names
	 * @param string sorting direction self::ASC or self::DESC
	 * @return IEntityCollection $this
	 */
	public function orderBy($row, $direction = self::ASC);

	/**
	 * Limits number of rows.
	 * @param int
	 * @param int
	 * @return IEntityCollection $this
	 */
	public function applyLimit($limit, $offset = NULL);

	/**
	 * Fetches the first row.
	 * @return IEntity|NULL
	 */
	public function fetch();

	/**
	 * Fetches all records.
	 * @return array of IEntity
	 */
	public function fetchAll();

	/**
	 * Fetches all records and returns associative tree.
	 * @param string associative descriptor
	 * @return array
	 */
	public function fetchAssoc($assoc);

	/**
	 * Fetches all records like $key => $value pairs.
	 * @param string associative key
	 * @param string value
	 * @return array
	 */
	public function fetchPairs($key = NULL, $value = NULL);

	/**
	 * Vraci kolekci entit dle kriterii.
	 * @param array
	 * @return IEntityCollection
	 */
	public function findBy(array $where);

	/**
	 * Vraci jednu entitu dle kriterii.
	 * @param array
	 * @return IEntity|NULL
	 */
	public function getBy(array $where);

	/** @return ArrayCollection */
	public function toArrayCollection();

	/** @return IEntityCollection */
	public function toCollection();

}

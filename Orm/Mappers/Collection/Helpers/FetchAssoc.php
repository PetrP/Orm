<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

/**
 * Helper for create associative tree from array of entities.
 * @author Petr Procházka
 * @package Orm
 * @subpackage Mappers\Collection\Helpers
 */
class FetchAssoc
{

	/**
	 * Returns associative tree.
	 * Examples:
	 * - associative descriptor: col1[]col2|col3
	 *   builds a tree:          $tree[$val1][$index][$val2][$val3] = {record}
	 * - associative descriptor: col1|col2|col3=col4
	 *   builds a tree:          $tree[$val1][$val2][$val3] = val4
	 * @param array of IEntity
	 * @param string associative descriptor
	 * @return array
	 * @throws InvalidArgumentException
	 * @throws NotSupportedException for modifer '->'
	 * Based on DibiResult::fetchAssoc()
	 */
	final public static function apply(array $rows, $assoc)
	{
		reset($rows);
		$row = current($rows);
		if (!$row) return array();  // empty result set

		if (strpos($assoc, ',') !== FALSE)
		{
			return self::oldFetchAssoc($rows, $assoc, $row);
		}

		$data = NULL;
		$assoc = preg_split('#(\[\]|->|=|\|)#', $assoc, NULL, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

		// check columns
		foreach ($assoc as $as)
		{
			// offsetExists ignores NULL in PHP 5.2.1, isset() surprisingly NULL accepts
			if ($as !== '[]' AND $as !== '=' AND $as !== '->' AND $as !== '|' AND !$row->hasParam($as))
			{
				throw new InvalidArgumentException("Unknown column '$as' in associative descriptor.");
			}
		}

		if (empty($assoc))
		{
			$assoc[] = '[]';
		}

		// make associative tree
		do {
			$x = & $data;

			// iterative deepening
			foreach ($assoc as $i => $as)
			{
				if ($as === '[]') // indexed-array node
				{
					$x = & $x[];

				}
				else if ($as === '=') // "value" node
				{
					$x = $row->{$assoc[$i+1]};
					continue 2;
				}
				else if ($as === '->') // "object" node
				{
					throw new NotSupportedException('FetchAssoc "object" node (->) is not supported');
				}
				else if ($as !== '|') // associative-array node
				{
					$x = & $x[(string) $row->$as];
				}
			}

			if ($x === NULL) // build leaf
			{
				$x = $row;
			}

		} while ($row = next($rows));

		unset($x);
		return $data;
	}

	/**
	 * @deprecated
	 * Returns associative tree.
	 * Examples:
	 * - associative descriptor: col1,#,col2,col3
	 *   builds a tree:          $tree[$val1][$index][$val2][$val3] = {record}
	 * @param array of IEntity
	 * @param string associative descriptor
	 * @param IEntity first one
	 * @return array
	 * @throws NotSupportedException for modifer '=' and '@'
	 * Based on DibiResult::oldFetchAssoc()
	 */
	private static function oldFetchAssoc(array $rows, $assoc, IEntity $row)
	{
		$data = NULL;
		$assoc = explode(',', $assoc);

		do {
			$x = & $data;

			foreach ($assoc as $i => $as)
			{
				if ($as === '#') // indexed-array node
				{
					$x = & $x[];
				}
				else if ($as === '=') // "record" node
				{
					throw new NotSupportedException('FetchAssoc "record" node (=) is not supported');
				}
				else if ($as === '@') // "object" node
				{
					throw new NotSupportedException('FetchAssoc "object" node (@) is not supported');
				}
				else // associative-array node
				{
					$x = & $x[(string) $row->$as];
				}
			}

			if ($x === NULL) // build leaf
			{
				$x = $row;
			}

		} while ($row = next($rows));

		unset($x);
		return $data;
	}

}

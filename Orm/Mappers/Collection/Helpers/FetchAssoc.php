<?php

namespace Orm;

use Nette\Object;
use Nette\InvalidArgumentException;
use Nette\NotSupportedException;

class FetchAssoc extends Object
{

	/**
	 * Fetches all records from table and returns associative tree.
	 * Examples:
	 * - associative descriptor: col1[]col2->col3
	 *   builds a tree:          $tree[$val1][$index][$val2]->col3[$val3] = {record}
	 * - associative descriptor: col1|col2->col3=col4
	 *   builds a tree:          $tree[$val1][$val2]->col3[$val3] = val4
	 * @param  string  associative descriptor
	 * @return DibiRow
	 * @throws InvalidArgumentException
	 */
	final public static function apply(array $rows, $assoc)
	{
		if (strpos($assoc, ',') !== FALSE) {
			return self::oldFetchAssoc($rows, $assoc);
		}

		reset($rows);
		$row = current($rows);
		if (!$row) return array();  // empty result set

		$data = NULL;
		$assoc = preg_split('#(\[\]|->|=|\|)#', $assoc, NULL, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

		// check columns
		foreach ($assoc as $as) {
			// offsetExists ignores NULL in PHP 5.2.1, isset() surprisingly NULL accepts
			if ($as !== '[]' && $as !== '=' && $as !== '->' && $as !== '|' && !$row->hasParam($as)) {
				throw new InvalidArgumentException("Unknown column '$as' in associative descriptor.");
			}
		}

		if (empty($assoc)) {
			$assoc[] = '[]';
		}

		// make associative tree
		do {
			$x = & $data;

			// iterative deepening
			foreach ($assoc as $i => $as) {
				if ($as === '[]') { // indexed-array node
					$x = & $x[];

				} elseif ($as === '=') { // "value" node
					$x = $row->{$assoc[$i+1]};
					continue 2;

				} elseif ($as === '->') { // "object" node
					throw new NotSupportedException('FetchAssoc "object" node (->) is not supported');
				} elseif ($as !== '|') { // associative-array node
					$x = & $x[$row->$as];
				}
			}

			if ($x === NULL) { // build leaf
				$x = $row;
			}

		} while ($row = next($rows));

		unset($x);
		return $data;
	}



	/**
	 * @deprecated
	 */
	private static function oldFetchAssoc(array $rows, $assoc)
	{
		reset($rows);
		$row = current($rows);
		if (!$row) return array();  // empty result set

		$data = NULL;
		$assoc = explode(',', $assoc);

		do {
			$x = & $data;

			foreach ($assoc as $i => $as) {
				if ($as === '#') { // indexed-array node
					$x = & $x[];

				} elseif ($as === '=') { // "record" node
					throw new NotSupportedException('FetchAssoc "record" node (=) is not supported');
				} elseif ($as === '@') { // "object" node
					throw new NotSupportedException('FetchAssoc "object" node (@) is not supported');
				} else { // associative-array node
					$x = & $x[$row->$as];
				}
			}

			if ($x === NULL) { // build leaf
				$x = $row;
			}

		} while ($row = next($rows));

		unset($x);
		return $data;
	}

}

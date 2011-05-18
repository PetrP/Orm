<?php

namespace Orm;


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

		if ($as === '->') { // must not be last
			array_pop($assoc);
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
					if ($x === NULL) {
						$x = clone $row;
						$x = & $x->{$assoc[$i+1]};
						$x = NULL; // prepare child node
					} else {
						$x = & $x->{$assoc[$i+1]};
					}

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

		// strip leading = and @
		$leaf = '@';  // gap
		$last = count($assoc) - 1;
		while ($assoc[$last] === '=' || $assoc[$last] === '@') {
			$leaf = $assoc[$last];
			unset($assoc[$last]);
			$last--;

			if ($last < 0) {
				$assoc[] = '#';
				break;
			}
		}

		do {
			$x = & $data;

			foreach ($assoc as $i => $as) {
				if ($as === '#') { // indexed-array node
					$x = & $x[];

				} elseif ($as === '=') { // "record" node
					if ($x === NULL) {
						$x = $row->toArray();
						$x = & $x[ $assoc[$i+1] ];
						$x = NULL; // prepare child node
					} else {
						$x = & $x[ $assoc[$i+1] ];
					}

				} elseif ($as === '@') { // "object" node
					if ($x === NULL) {
						$x = clone $row;
						$x = & $x->{$assoc[$i+1]};
						$x = NULL; // prepare child node
					} else {
						$x = & $x->{$assoc[$i+1]};
					}


				} else { // associative-array node
					$x = & $x[$row->$as];
				}
			}

			if ($x === NULL) { // build leaf
				if ($leaf === '=') {
					$x = $row->toArray();
				} else {
					$x = $row;
				}
			}

		} while ($row = next($rows));

		unset($x);
		return $data;
	}

}

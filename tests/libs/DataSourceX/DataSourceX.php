<?php
/**
 * DibiDataSource pro mysql.
 *
 * @version 0.2.3
 * @author Petr Procházka (petr@petrp.cz)
 * @link http://petrp.cz/dibidatasource-pro-mysql
 * @license "New" BSD License
 */

require_once dirname(__FILE__) . '/BaseDataSourceX.php';

/**
 * DibiDataSource bez subselectu pro mysql.
 * Parsuje sql!
 *
 *
 * Nepodporuje uplne vse a nechova se 100% stejne.
 *  - Je omezen jen na mysql, protoze jine db nemaji problem.
 *  - self::count() muze byt pomaly pri vysokych limitech
 *  - Nepodporuje nektere veci v selectu.
 *  - Použití LIMIT ve zdroji může dát jiný výsledek než klasický DS.
 *  - Používání aliasů DibiDataSourceX('SELECT id as foo') je potřeba důsledně escapovat např ->where('[foo] = 1')
 *  - U selectu všeho při více tabulkách můžou být problémy ('SELECT table.*, table2.*')
 *  - Neumožnuje UNION
 *
 * POZOR VYVOJOVA VERZE
 * @author Petr Procházka petr@petrp.cz
 */
class DibiDataSourceX extends BaseDibiDataSourceX
{

	const DELIMITER = "\x00\x00";

	const DELIMITER_ENCODED = "\\x00\\x00";

	/** @var array */
	private $coded = array();

	/** @var bool */
	private $classicDataSoure = false;

	/**
	 * @param  string  SQL command or table or view name, as data source
	 * @param  DibiConnection  connection
	 */
	public function __construct($sql, DibiConnection $connection)
	{
		$this->connection = $connection;
		parent::__construct(' ', $connection);
		// protoze getConnection je final a $connection je private

		if (strpbrk($sql, " \t\r\n") === FALSE)
		{
			$this->sql = $this->connectionTranslate('SELECT * FROM %n', $sql); // table name
		}
		else
		{
			$this->sql = $sql;
		}

		$driver = $this->connection->getDriver();
		if (!($driver instanceof DibiMySqlDriver) AND !($driver instanceof DibiMySqliDriver))
		{
			$this->classicDataSoure = true;
			//throw new NotSupportedException('Only for mysql; use clasic DibiDataSource instead');
		}
	}

	/**
	 * Schová vše v závorce a mezy znaky `'
	 * @see self::decode()
	 * @param string
	 * @return string
	 */
	private function encode($sql, $itentifier = true, $string = true, $sub = true, $subSelect = false)
	{
		if (strpos($sql, self::DELIMITER) !== false)
		{
			$sql = str_replace(self::DELIMITER, '', $sql);
		}

		if ($sub)
		{
			while (preg_match('#\(.*\)#is', $sql))
			{
				$sql = preg_replace_callback('#\([^\(\)]*\)#is', array($this, 'encodeCb'), $sql);
			}
		}
		else if ($subSelect)
		{
			while (preg_match('#\(\s*SELECT.*\)#is', $sql))
			{
				$sql = preg_replace_callback('#\(\s*SELECT.*\)#is', array($this, 'encodeSelectCb'), $sql);
			}
		}

		if ($string)
		{
			$sql = preg_replace_callback('#\\\\\'#is', array($this, 'encodeCb'), $sql);
			$sql = preg_replace_callback('#\'[^\']*\'#is', array($this, 'encodeCb'), $sql);
		}

		if ($itentifier)
		{
			$sql = preg_replace_callback('#``#is', array($this, 'encodeCb'), $sql);
			$sql = preg_replace_callback('#`[^`]*`#is', array($this, 'encodeCb'), $sql);
		}

		return $sql;
	}

	/**
	 * Reverzní funkce, vrátí schované zpět.
	 * @see self::encode()
	 * @param string
	 * @return string
	 */
	private function decode($sql)
	{
		while (strpos($sql, self::DELIMITER) !== false)
		{
			$sql = preg_replace_callback('#' . self::DELIMITER_ENCODED . '([0-9]+)' . self::DELIMITER_ENCODED . '#Sis', array($this, 'decodeCb'), $sql);
		}

		return $sql;
	}

	/**
	 * callback
	 * @see self::encode()
	 * @see self::$coded
	 * @param array
	 * @return string
	 */
	private function encodeCb($match)
	{
		$this->coded[] = $match[0];
		end($this->coded);
		return self::DELIMITER . key($this->coded) . self::DELIMITER;
	}
	/**
	 * callback
	 * @see self::encode()
	 * @see self::$coded
	 * @param array
	 * @return string
	 */
	private function encodeSelectCb($match)
	{
		$c = 0;

		foreach (str_split($match[0]) as $position=>$ch)
		{
			if ($ch === '(') $c++;
			else if ($ch === ')') $c--;
			if ($c === 0) break;
		}

		$position++;

		return
			$this->encodeCb(array(substr($match[0], 0, $position))) .
			substr($match[0], $position)
		;
	}

	/**
	 * callback
	 * @see self::decode()
	 * @see self::$coded
	 * @param array
	 * @return string
	 */
	private function decodeCb($match)
	{
		$n = $match[1];
		if (!isset($this->coded[$n])) throw new InvalidStateException;
		$r = $this->coded[$n];
		unset($this->coded[$n]);
		return $r;
	}

	/**
	 * Upravi encodovane sql pro zobrazeni napr v chybe.
	 * @param string
	 * @return string
	 */
	private function decodeToPrint($sql)
	{
		return trim(preg_replace('#\s+#s', ' ', preg_replace('#' . self::DELIMITER_ENCODED . '([0-9]+)' . self::DELIMITER_ENCODED . '#Sis', '?', $sql)));
	}

	/**
	 * Returns SQL query.
	 * @param bool $throwException = false
	 * @param bool $forCount = false internal
	 * @return string
	 */
	public function __toString(/*$throwException = false, $forCount = false*/)
	{
		static $aggregateFunction = array(
			'AVG', 'BIT_AND', 'BIT_OR', 'BIT_XOR', 'COUNT', 'GROUP_CONCAT',
			'MAX', 'MIN', 'STD', 'STDDEV_POP', 'STDDEV_SAMP', 'STDDEV',
			'SUM', 'VAR_POP', 'VAR_SAMP', 'VARIANCE', 'DISTINCT',
		);

		try {

			$throwException = func_num_args() ? func_get_arg(0) : false;
			$forCount = func_num_args() >= 2 ? func_get_arg(1) : false;

			if ($this->classicDataSoure)
			{
				if ($forCount) throw new NotSupportedException();
				return BaseDibiDataSourceX::__toString();
			}

			if (
				!$forCount AND
				!$this->conds AND
				!$this->sorting AND
				!$this->cols AND
				$this->limit === NULL AND
				$this->offset === NULL
			)
			{
				return $this->sql;
			}

			$sql = $this->encode($this->sql);

			if (stripos($sql, 'UNION'))
			{
				throw new NotSupportedException("UNION is not supported: {$this->decodeToPrint($sql)}");
			}

			if (!preg_match('#^\s*
				SELECT           \s+    (.+)                        \s*
				(?:  FROM        \s+    (.+)                        \s*  )?
				(?:  WHERE       \s+    (.+)                        \s*  )?
				(?:  GROUP\s+BY  \s+    (.+)                        \s*  )?
				(?:  HAVING      \s+    (.+)                        \s*  )?
				(?:  ORDER\s+BY  \s+    (.+)                        \s*  )?
				(?:  LIMIT       \s+    ([0-9]+(?:\s*,\s*[0-9]+)?)  \s*  )?
				(?:  OFFSET      \s+    ([0-9]+)                    \s*  )?
				$#xUsi', $sql, $match))
			{
				throw new NotSupportedException("You have an error in your SQL syntax, or you are using an unsupported clause: {$this->decodeToPrint($sql)}");
			}

			foreach (array(
				'select' => 1,
				'from' => 2,
				'where' => 3,
				'group' => 4,
				'having' => 5,
				'order' => 6,
				'limit' => 7,
				'offset' => 8,
			) as $n=>$i)
			{
				$$n = NULL;

				if (!empty($match[$i]))
				{
					if ($n === 'select' AND ($this->cols OR $forCount))
					{
						$hasAggregateFunction = false;
						foreach ($aggregateFunction as $f)
						{
							if (stripos($match[$i], $f) !== false)
							{
								$hasAggregateFunction = true;
								if ($this->cols)
								{
									if ($forCount) return 'SELECT COUNT(*) FROM (' . BaseDibiDataSourceX::__toString() . ') AS t';
									return BaseDibiDataSourceX::__toString();
									// ma aggregacni funkci a upravuje cols musi se pouzit subselect, protoze jinak se ta funkce prepise a dostanu jiny vysledek
									// muze byt zabijak vykonu, lepsi je datasource nepouzit
									// todo nevyhazovat radeji chybu aby to nikdo nepouzival?
								}
								break;
							}
						}
					}
					$$n = trim($this->decode($match[$i]));
				}

				if (empty($$n)) $$n = NULL;
			}

			$aliasesNew = array();
			$aliasesAll = array();
			$aliasStar = NULL;

			foreach (explode(',', $this->encode($select, false)) as $exp)
			{
				if (preg_match('#^(.+)AS\s*(`?)(.+)\\2\s*$#is', $exp, $match))
				{
					$aliasesAll[trim($match[3])] = trim($this->decode($match[1]));
				}
				elseif (preg_match('#^\s*(`?)(.+)\\1\.(`?)\*\\3\s*$#is', $exp, $match))
				{
					if ($aliasStar === NULL) $aliasStar = trim($match[2]);
					else $aliasStar = false;
				}
				elseif (preg_match('#^\s*(`?)(.+)\\1\.(`?)(.+)\\3\s*$#is', $exp, $match))
				{
					$aliasesNew[trim($match[4])] = trim($this->decode($match[0]));
				}
			}

			$aliases = $aliasesNew + $aliasesAll;
			if ($this->conds)
			{
				if ($where) $where = "($where) AND ";
				$where .= $this->replaceAliasis($this->connectionTranslate('%and', $this->conds), $aliasesNew, $aliasStar);
			}

			if ($this->sorting)
			{
				$order = $this->replaceAliasis($this->connectionTranslate('%by', $this->sorting), $aliasesNew, $aliasStar) . ($order ? ", $order" : '');
			}

			if ($this->cols)
			{
				$driver = $this->connection->getDriver();
				$cols = array();

				foreach ($this->cols as $col => $as)
				{
					if (is_int($col))
					{
						$col = $as;
						$as = NULL;
					}

					if (isset($aliases[$col]))
					{
						if ($as) $cols[] = $aliases[$col] . ' AS ' . $driver->escape($as, Dibi::IDENTIFIER);
						else $cols[] = $aliases[$col] . ' AS ' . $driver->escape($col, Dibi::IDENTIFIER);
					}
					else if ($as)
					{
						$cols[] = $driver->escape($col, Dibi::IDENTIFIER) . ' AS ' . $driver->escape($as, Dibi::IDENTIFIER);
					}
					else
					{
						$cols[] = $driver->escape($col, Dibi::IDENTIFIER);
					}
				}

				$select = implode(', ', $cols);
			}


			if ($limit !== NULL AND strpos($limit, ',') !== false)
			{
				$tmp = explode(',', $limit);
				$limit = trim($tmp[1]);
				$offset = trim($tmp[0]);
			}

			if ($limit !== NULL) $limit = intval($limit, '10');
			if ($offset !== NULL) $offset = intval($offset, '10');

			$newLimit = $newOffset = NULL;

			if ($offset !== NULL AND $this->offset !== NULL)
			{
				$newOffset = $this->offset+$offset;
			}
			else if ($this->offset !== NULL)
			{
				$newOffset = $this->offset;
			}
			else if ($offset !== NULL)
			{
				$newOffset = $offset;
			}

			if ($limit !== NULL AND $this->limit !== NULL)
			{
				$newLimit = min($this->limit, $limit-$this->offset);
			}
			else if ($this->limit !== NULL)
			{
				$newLimit = $this->limit;
			}
			else if ($limit !== NULL)
			{
				$newLimit = $limit;
			}


			if ($forCount AND
				$newLimit === NULL AND $newOffset === NULL AND
					// ma limit
					// nejde resit jinak nez subselectem, pro rozumne limity ale neni problem
				$group === NULL AND $having === NULL AND
					// ma group by
					// ve vetsine pripadu nejde resit jinak, rozhodne to nejde globalizovat
					// muze byt zabijak vykonu, lepsi je datasource nepouzit
					// todo nevyhazovat radeji chybu aby to nikdo nepouzival?
				!$hasAggregateFunction
					// ma agregacni funkci
					// plati pro to to same co pro group by
			)
			{
				// pro ostatni varianty neni potreba pouzit subselect
				$select = $this->connectionTranslate('COUNT(*)');
				$forCount = false;
			}

			$string =
				"\nSELECT " . ($select ? $select : '*') . "\n" .
				($from ? "	FROM $from\n" : '') .
				$this->replaceAliasis(
					($where ? "	WHERE $where\n" : '')
				, $aliasesAll) .
					($group ? "	GROUP BY $group\n" : '') .
				$this->replaceAliasis(
					($having ? "	HAVING $having\n" : '') .
					($order ? "	ORDER BY $order \n" : '')
				, $aliasesAll) .
				(($l = trim($this->connectionTranslate('%ofs %lmt', $newOffset, $newLimit))) ? "	$l\n" : '')
			;

			if ($forCount) // has limit or group
			{
				return "SELECT COUNT(*) FROM (" . $string . ") t";
			}
			return $string;

		} catch (Exception $e) {

			if ($throwException)
			{
				throw $e;
			}

			Debug::toStringException($e);
		}
	}

	/**
	 * Nahradí v sql aliasy za původní.
	 * Jen u escapovaných názvů.
	 * @param string
	 * @param array
	 * @return string
	 */
	private function replaceAliasis($sql, $aliases, $star = NULL)
	{
		$esql = $this->encode($sql, false, true, false, true);

		foreach ($aliases as $col => $origin)
		{
			$esql = preg_replace('#(?:(?<!\.|\s)|(?<!\.|\s)(\s+))`' . preg_quote($col, '#') . '`(?:(?>!\.|\s)|(\s+)(?>!\.|\s))#s', '$1' . $origin . '$2', $esql); // todo prevede jen kdyz je escapovano
		}
		if ($star)
		{
			$esql = preg_replace('#(?:(?<!\.|\s)|(?<!\.|\s)(\s+))`([^`]+)`(?:(?>!\.|\s)|(\s+)(?>!\.|\s))#si', '$1' . "`{$star}`.`\$2`" . '$3', $esql);
		}

		return $this->decode($esql);
	}

	/**
	 * Returns the number of rows in a given data source.
	 * @return int
	 */
	public function count()
	{
		if ($this->count === NULL)
		{
			if ($this->classicDataSoure)
			{
				return BaseDibiDataSourceX::count();
			}
			$this->count = ($this->conds OR $this->offset !== NULL OR $this->limit !== NULL)
				? (int) $this->connection->nativeQuery(
					$this->__toString(true, true)
				)->fetchSingle()
				: $this->getTotalCount();
		}

		return $this->count;
	}

	/**
	 * Returns the number of rows in a given data source.
	 * @return int
	 */
	public function getTotalCount()
	{
		if ($this->totalCount === NULL)
		{
			if ($this->classicDataSoure)
			{
				return BaseDibiDataSourceX::getTotalCount();
			}
			$ds = new self($this->sql, $this->connection);
			$this->totalCount = (int) $this->connection->nativeQuery($ds->__toString(true, true))->fetchSingle();
		}

		return $this->totalCount;
	}

}

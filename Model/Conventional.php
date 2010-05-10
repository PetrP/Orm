<?php


class Conventional extends Object implements IConventional
{
	public function format($data, $entityName)
	{
		return (array) $data;
	}
	
	public function unformat($data, $entityName)
	{
		return (array) $data;
	}
	
	/**
	 * fk
	 * @param  string
	 * @return string
	 */
	public function foreignKeyFormat($s)
	{
		return $s;
	}
	
}

class SqlConventional extends Conventional
{
	private static $cache = array(); // todo je potreba ukladat podle nazvu trydy, jinak se pri pouziti nekolika trid bude kolidovat.
	
	/**
	 * camelCase -> underscore_separated.
	 * @param  string
	 * @return string
	 */
	protected function formatKey($key) // todo rename
	{
		$s = preg_replace('#(.)(?=[A-Z])#', '$1_', $key);
		$s = strtolower($s);
		return $s;
	}
	
	/**
	 * underscore_separated -> camelCase.
	 * @param  string
	 * @return string
	 */
	protected function unformatKey($key) // todo rename
	{
		$s = strtolower($key);
		$s = preg_replace('#_(?=[a-z])#', ' ', $s);
		$s = substr(ucwords('x' . $s), 1);
		$s = str_replace(' ', '', $s);
		return $s;
	}
	
	public function format($data, $entityName)
	{
		$this->loadFk($entityName);
		
		$result = array();
		foreach ($data as $key => $value)
		{
			if (isset(self::$cache['fk'][$entityName][$key]))
			{
				$k = self::$cache['fk'][$entityName][$key];
			}
			else if (isset(self::$cache[$key]))
			{
				$k = self::$cache[$key];
			}
			else
			{
				$k = self::$cache[$key] = $this->unformatKey($key);
			}
			$result[$k] = $value;
		}
		return $result;
	}
	
	private function loadFk($entityName)
	{
		if (!isset(self::$cache['fk'][$entityName]))
		{
			$fk = array();
			$unfk = array();
			foreach (Entity::getFK($entityName) as $n => $foo)
			{
				$fk[$tmp = $this->foreignKeyFormat($this->unformatKey($n))] = $n . '__fk__id'; // todo constant
				$unfk[$n] = $tmp; // todo constant
			}
			self::$cache['fk'][$entityName] = $fk;
			self::$cache['unfk'][$entityName] = $unfk;
		}
	}
	
	public function unformat($data, $entityName)
	{
		$this->loadFk($entityName);
		
		$result = array();
		foreach ($data as $key => $value)
		{
			if (isset(self::$cache['unfk'][$entityName][$key]))
			{
				$k = self::$cache['unfk'][$entityName][$key];
			}
			else if (isset(self::$cache[$key]))
			{
				$k = self::$cache[$key];
			}
			else
			{
				$k = self::$cache[$key] = $this->unformatKey($key);
			}
			$result[$k] = $value;
		}
		return $result;
	}
	
	/**
	 * fk
	 * @param  string
	 * @return string
	 */
	public function foreignKeyFormat($s)
	{
		return $s . '_id';
	}
	
}
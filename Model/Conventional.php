<?php


class Conventional extends Object implements IConventional
{
	public function format($data)
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
	private static $cache = array();
	
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
	
	public function format($data)
	{
		$result = array();
		foreach ($data as $key => $value)
		{
			if (!isset(self::$cache[$key]))
			{
				self::$cache[$key] = $this->formatKey($key);
			}
			$result[self::$cache[$key]] = $value;
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
<?php

require_once dirname(__FILE__) . '/IConventional.php';

class SqlConventional extends Object implements IConventional
{
	private static $staticCache = array();

	private $cache = array();
	
	public function __construct(Mapper $mapper)
	{
		$this->cache = & self::$staticCache[$mapper->getRepository()->getRepositoryName()];
		$this->loadFk((array) $mapper->getRepository()->getEntityClassName());
	}
	
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

	/**
	 * fk
	 * @param  string
	 * @return string
	 */
	protected function foreignKeyFormat($s)
	{
		return $s . '_id';
	}

	final public function format($data)
	{
		$result = array();
		foreach ($data as $key => $value)
		{
			if (isset($this->cache['fk'][$key]))
			{
				$k = $this->cache['fk'][$key];
			}
			else if (isset($this->cache[$key]))
			{
				$k = $this->cache[$key];
			}
			else
			{
				$k = $this->cache[$key] = $this->formatKey($key);
			}
			$result[$k] = $value;
		}
		return $result;
	}

	final private function loadFk(array $entityNames)
	{
		if (!isset($this->cache['fk']))
		{
			$result = array();
			if ($this->foreignKeyFormat('test') !== 'test') // pokracovat jen kdyz se fk format lisi
			{
				foreach ($entityNames as $entityName)
				{
					foreach (Entity::getFK($entityName) as $name => $foo)
					{
						$fk = $this->foreignKeyFormat($this->formatKey($name));
						$result[$fk] = $name;
						$result[$name] = $fk;
					}
				}
			}
			$this->cache['fk'] = $result;
		}
	}

	final public function unformat($data)
	{
		$result = array();
		foreach ($data as $key => $value)
		{
			if (isset($this->cache['fk'][$key]))
			{
				$k = $this->cache['fk'][$key];
			}
			else if (isset($this->cache[$key]))
			{
				$k = $this->cache[$key];
			}
			else
			{
				$k = $this->cache[$key] = $this->unformatKey($key);
			}
			$result[$k] = $value;
		}
		return $result;
	}

}

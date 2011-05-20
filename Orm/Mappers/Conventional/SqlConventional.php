<?php

namespace Orm;

use Nette\Object;
use Nette\DeprecatedException;

require_once dirname(__FILE__) . '/IConventional.php';

/**
 * Rozdily nazvu klicu v entite a v ulozisti.
 * V entite camelCase
 * V ulozisti underscore_separated
 * @todo Rename?
 */
class SqlConventional extends Object implements IConventional
{

	/** @var array */
	private $cache = array();

	/** @param IMapper */
	public function __construct(IMapper $mapper)
	{
		$this->loadFk((array) $mapper->getRepository()->getEntityClassName());
	}

	/**
	 * Prejmenuje klice z entity do formatu uloziste
	 * @param array|Traversable
	 * @return array
	 */
	final public function formatEntityToStorage($data)
	{
		$result = array();
		foreach ($data as $key => $value)
		{
			if (isset($this->cache['fk']['entity'][$key]))
			{
				$k = $this->cache['fk']['entity'][$key];
			}
			else if (isset($this->cache['entity'][$key]))
			{
				$k = $this->cache['entity'][$key];
			}
			else
			{
				$k = $this->cache['entity'][$key] = $this->storageFormat($key);
			}
			$result[$k] = $value;
		}
		return $result;
	}

	/**
	 * Prejmenuje klice z uloziste do formatu entity
	 * @param array|Traversable
	 * @return array
	 */
	final public function formatStorageToEntity($data)
	{
		$result = array();
		foreach ($data as $key => $value)
		{
			if (isset($this->cache['fk']['storage'][$key]))
			{
				$k = $this->cache['fk']['storage'][$key];
			}
			else if (isset($this->cache['storage'][$key]))
			{
				$k = $this->cache['storage'][$key];
			}
			else
			{
				$k = $this->cache['storage'][$key] = $this->entityFormat($key);
			}
			$result[$k] = $value;
		}
		return $result;
	}

	/**
	 * camelCase -> underscore_separated.
	 * @param  string
	 * @return string
	 */
	protected function storageFormat($key)
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
	protected function entityFormat($key)
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

	/**
	 * @todo
	 * @param IRepository
	 * @param IRepository
	 * @return string
	 */
	public function getManyToManyTable(IRepository $first, IRepository $second)
	{
		return str_replace('\\', '_', $first->getRepositoryName() . '_x_' . $second->getRepositoryName());
	}

	/**
	 * @todo
	 * @param string
	 * @return string
	 */
	public function getManyToManyParam($param)
	{
		$param = key($this->formatEntityToStorage(array($param => NULL)));
		return $this->foreignKeyFormat($param);
	}

	/**
	 * Nastavi $this->cache['fk']
	 * @param array of entityname
	 */
	final private function loadFk(array $entityNames)
	{
		$result = array();
		if ($this->foreignKeyFormat('test') !== 'test') // pokracovat jen kdyz se fk format lisi
		{
			foreach ($entityNames as $entityName)
			{
				foreach (MetaData::getEntityRules($entityName) as $name => $rule)
				{
					if ($rule['relationship'] !== MetaData::ManyToOne AND $rule['relationship'] !== MetaData::OneToOne) continue;
					$fk = $this->foreignKeyFormat($this->storageFormat($name));
					$result['storage'][$fk] = $name;
					$result['entity'][$name] = $fk;
				}
			}
		}
		$this->cache['fk'] = $result;
	}


	/** @deprecated */
	final public function format($data){throw new DeprecatedException('Use Orm\SqlConventional::formatEntityToStorage() instead');}
	/** @deprecated */
	final public function unformat($data){throw new DeprecatedException('Use Orm\SqlConventional::formatStorageToEntity() instead');}
	/** @deprecated */
	final protected function formatKey($key){throw new DeprecatedException('Use Orm\SqlConventional::storageFormat() instead');}
	/** @deprecated */
	final protected function unformatKey($key){throw new DeprecatedException('Use Orm\SqlConventional::entityFormat() instead');}
	/** @deprecated */
	final public function getManyToManyTableName(IRepository $first, IRepository $second){throw new DeprecatedException('Use Orm\SqlConventional::getManyToManyTable() instead');}
}

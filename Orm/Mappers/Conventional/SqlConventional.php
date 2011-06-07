<?php

namespace Orm;

use Nette\Object;

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
		$this->loadFk((array) $mapper->getRepository()->getEntityClassName(), $mapper->getRepository()->getModel());
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
	 * @return string
	 */
	public function getTable(IRepository $repository)
	{
		return str_replace('\\', '_', $repository->getRepositoryName());
	}

	/**
	 * @todo
	 * @param IRepository
	 * @param IRepository
	 * @return string
	 */
	public function getManyToManyTable(IRepository $first, IRepository $second)
	{
		return $this->getTable($first) . '_x_' . $this->getTable($second);
	}

	/**
	 * @todo
	 * @param string
	 * @return string
	 */
	public function getManyToManyParam($param)
	{
		$param = $this->formatEntityToStorage(array($param => NULL));
		return $this->foreignKeyFormat(key($param));
	}

	/**
	 * Nastavi $this->cache['fk']
	 * @param array of entityname
	 * @param IRepositoryContainer
	 */
	final private function loadFk(array $entityNames, IRepositoryContainer $model)
	{
		$result = array();
		if ($this->foreignKeyFormat('test') !== 'test') // pokracovat jen kdyz se fk format lisi
		{
			foreach ($entityNames as $entityName)
			{
				foreach (MetaData::getEntityRules($entityName, $model) as $name => $rule)
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

}

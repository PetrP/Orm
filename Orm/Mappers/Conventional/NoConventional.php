<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use Nette\Object;

/**
 * Naming conventions in storage.
 * Makes no difference. Stores same.
 */
class NoConventional extends Object implements IDatabaseConventional
{

	/** @var bool */
	private $isNonStandartPrimaryKey;

	public function __construct()
	{
		$this->isNonStandartPrimaryKey = $this->getPrimaryKey() !== 'id';
	}

	/**
	 * Prejmenuje klice z entity do formatu uloziste
	 * @param array|Traversable
	 * @return array
	 */
	public function formatEntityToStorage($data)
	{
		$data = (array) $data;
		if ($this->isNonStandartPrimaryKey)
		{
			$this->renameKey($data, 'id', $this->getPrimaryKey());
		}
		return $data;
	}

	/**
	 * Prejmenuje klice z uloziste do formatu entity
	 * @param array|Traversable
	 * @return array
	 */
	public function formatStorageToEntity($data)
	{
		$data = (array) $data;
		if ($this->isNonStandartPrimaryKey)
		{
			$this->renameKey($data, $this->getPrimaryKey(), 'id');
		}
		return $data;
	}

	/** @return string */
	public function getPrimaryKey()
	{
		return 'id';
	}

	/**
	 * @param IRepository
	 * @return string
	 */
	public function getTable(IRepository $repository)
	{
		$helper = $repository->getModel()->getContext()->getService('repositoryHelper', 'Orm\RepositoryHelper');
		return str_replace('\\', '_', $helper->normalizeRepository($repository));
	}

	/**
	 * @param IRepository
	 * @param IRepository
	 * @return string
	 */
	public function getManyToManyTable(IRepository $source, IRepository $target)
	{
		return $this->getTable($source) . '_x_' . $this->getTable($target);
	}

	/**
	 * @param string
	 * @return string
	 */
	public function getManyToManyParam($param)
	{
		if ($param AND substr_compare($param, 's', -1) === 0)
		{
			$param = substr_replace($param, '', -1);
		}
		return $param;
	}

	/**
	 * Renames key in array.
	 * @param array
	 * @param mixed
	 * @param mixed
	 * @return void
	 */
	private function renameKey(array & $arr, $oldKey, $newKey)
	{
		$foo = array($oldKey => NULL);
		$offset = array_search(key($foo), array_keys($arr), true);
		if ($offset !== false)
		{
			$keys = array_keys($arr);
			$keys[$offset] = $newKey;
			$arr = array_combine($keys, $arr);
		}
	}

}

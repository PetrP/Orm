<?php

namespace Orm;

use Nette\Object;

require_once __DIR__ . '/IConventional.php';

/**
 * Rozdily nazvu klicu v entite a v ulozisti.
 * Nedela zadny rozdil, uklada stejne.
 */
class NoConventional extends Object implements IConventional
{

	/**
	 * Prejmenuje klice z entity do formatu uloziste
	 * @param array|Traversable
	 * @return array
	 */
	public function formatEntityToStorage($data)
	{
		return (array) $data;
	}

	/**
	 * Prejmenuje klice z uloziste do formatu entity
	 * @param array|Traversable
	 * @return array
	 */
	public function formatStorageToEntity($data)
	{
		return (array) $data;
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
	public function getManyToManyTable(IRepository $source, IRepository $target)
	{
		return $this->getTable($source) . '_x_' . $this->getTable($target);
	}

	/**
	 * @todo
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

}

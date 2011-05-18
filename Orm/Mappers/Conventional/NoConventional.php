<?php

namespace Orm;

use Nette\Object;
use Nette\DeprecatedException;

require_once dirname(__FILE__) . '/IConventional.php';

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
	 * @param IRepository
	 * @return string
	 */
	public function getManyToManyTable(IRepository $first, IRepository $second)
	{
		return $first->getRepositoryName() . '_x_' . $second->getRepositoryName();
	}

	/**
	 * @todo
	 * @param string
	 * @return string
	 */
	public function getManyToManyParam($param)
	{
		return $param;
	}

	/** @deprecated */
	final public function format($data){throw new DeprecatedException('Use NoConventional::formatEntityToStorage() instead');}
	/** @deprecated */
	final public function unformat($data){throw new DeprecatedException('Use NoConventional::formatStorageToEntity() instead');}
	/** @deprecated */
	final public function getManyToManyTableName(IRepository $first, IRepository $second){throw new DeprecatedException('Use NoConventional::getManyToManyTable() instead');}
}

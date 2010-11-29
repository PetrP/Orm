<?php

require_once dirname(__FILE__) . '/IConventional.php';

class NoConventional extends Object implements IConventional
{
	public function __construct(IMapper $repository)
	{

	}

	public function formatEntityToStorage($data)
	{
		return (array) $data;
	}

	public function formatStorageToEntity($data)
	{
		return (array) $data;
	}

	public function getManyToManyTableName(IRepository $first, IRepository $second)
	{
		return $first->getRepositoryName() . '_x_' . $second->getRepositoryName();
	}


	/** @ignore @deprecated */
	final public function format($data)
	{
		throw new DeprecatedException();
		return $this->formatEntityToStorage($data);
	}
	/** @ignore @deprecated */
	final public function unformat($data)
	{
		throw new DeprecatedException();
		return $this->formatStorageToEntity($data);
	}
}

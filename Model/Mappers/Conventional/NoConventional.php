<?php

require_once dirname(__FILE__) . '/IConventional.php';

class NoConventional extends Object implements IConventional
{
	public function __construct(Mapper $repository)
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

	public function getManyToManyTableName(Repository $first, Repository $second)
	{
		return $first->getRepositoryName() . '_x_' . $second->getRepositoryName();
	}

	// todo deprecated
	public function format($data)
	{
		throw new DeprecatedException();
		return $this->formatEntityToStorage($data);
	}
	public function unformat($data)
	{
		throw new DeprecatedException();
		return $this->formatStorageToEntity($data);
	}
}


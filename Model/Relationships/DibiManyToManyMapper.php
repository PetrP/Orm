<?php

require_once dirname(__FILE__) . '/IManyToManyMapper.php';

class DibiManyToManyMapper extends Object implements IManyToManyMapper
{
	public $firstParamName;
	public $secondParamName;

	private $firstRepository;
	private $secondRepository;

	private $parentIsFirst;

	private $connection;

	public function __construct(DibiConnection $connection)
	{
		$this->connection = $connection;
	}

	protected function getFirstParamName()
	{
		return 'first';
		// todo
		$conventional = $this->firstRepository->getMapper()->getConventional();
		return $conventional->foreignKeyFormat('first');
	}

	protected function getSecondParamName()
	{
		return 'second';
		// todo
		$conventional = $this->secondRepository->getMapper()->getConventional();
		return $conventional->foreignKeyFormat('second');
	}

	final protected function getParentParamName()
	{
		return $this->parentIsFirst ? $this->firstParamName : $this->secondParamName;
	}

	final protected function getChildParamName()
	{
		return $this->parentIsFirst ? $this->secondParamName : $this->firstParamName;
	}

	public function setParams($parentIsFirst, IRepository $firstRepository, IRepository $secondRepository)
	{
		$this->parentIsFirst = $parentIsFirst;
		$this->firstRepository = $firstRepository;
		$this->secondRepository = $secondRepository;
		if (!$this->firstParamName) $this->firstParamName = $this->getFirstParamName();
		if (!$this->secondParamName) $this->secondParamName = $this->getSecondParamName();
	}

	protected function getTableName()
	{
		$conventional = $this->firstRepository->getMapper()->getConventional();
		return $conventional->getManyToManyTableName($this->firstRepository, $this->secondRepository);
	}

	public function add(IEntity $parent, array $ids)
	{
		$this->begin();
		$connection = $this->connection;
		$table = $this->getTableName();
		$parentId = $parent->id;
		$parentParamName = $this->getParentParamName();
		$childParamName = $this->getChildParamName();
		foreach ($ids as $childId)
		{
			// todo jeden dotaz
			$connection->insert($table, array(
				$parentParamName => $parentId,
				$childParamName => $childId,
			))->execute();
		}
	}

	public function remove(IEntity $parent, array $ids)
	{
		$this->begin();
		$connection = $this->connection;
		$parentId = $parent->id;
		$connection->delete($this->getTableName())
			->where('%n = %s AND %n IN %in',
				$this->getParentParamName(), $parentId,
				$this->getChildParamName(), $ids
			)->execute()
		;
	}

	public function load(IEntity $parent)
	{
		if (!isset($parent->id)) return array();
		$table = $this->getTableName();
		$connection = $this->connection;
		return $connection->select($this->getChildParamName())
			->from($this->getTableName())
			->where('%n = %s',
				$this->getParentParamName(), $parent->id
			)->fetchPairs()
		;
	}
}

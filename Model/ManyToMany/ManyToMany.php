<?php
// todo moznost mit parametry/ data
// ->add($entity, array('time' => time()))
// todo rict entity ze se zmenila
abstract class ManyToMany extends Object implements IteratorAggregate
{
	protected $parentRepository;
	protected $childRepository;

	protected $parent;
	protected $childs;

	protected $parentIsFirst;

	public function __construct(Entity $entity)
	{
		$firstRepository = $this->getFirstRepository();
		$secondRepository = $this->getSecondRepository();

		if ($firstRepository->isEntity($entity))
		{
			$this->parentRepository = $firstRepository;
			$this->childRepository = $secondRepository;
			$this->parentIsFirst = true;
		}
		else if ($this->getSecondRepository()->isEntity($entity))
		{
			$this->parentRepository = $secondRepository;
			$this->childRepository = $firstRepository;
			$this->parentIsFirst = false;
		}
		else
		{
			throw new UnexpectedValueException();
		}
		$this->parent = $entity;
	}

	/** @return Repository */
	protected function getFirstRepository()
	{
		return Model::getRepository(substr(get_class($this), 0, strpos(get_class($this), 'To')));
	}

	/** @return Repository */
	protected function getSecondRepository()
	{
		return Model::getRepository(substr(get_class($this), strpos(get_class($this), 'To') + 2));
	}

	final public function add($entity)
	{
		if (!($entity instanceof Entity))
		{
			$entity = $this->childRepository->getById($entity);
		}

		if (!$this->childRepository->isEntity($entity))
		{
			throw new UnexpectedValueException();
		}

		$this->get();

		$this->childs[] = $entity;
		return $this;
	}

	final public function get()
	{
		if ($this->childs === NULL)
		{
			$data = $this->load();
			$this->childs = $data ? $this->childRepository->findById($data)->fetchAll() : array();
		}
		return $this->childs;
	}

	final public function set(array $array)
	{
		$this->childs = array();
		array_map(array($this, 'add'), $array);
		return $this;
	}

	final public function getIterator()
	{
		return new ArrayIterator($this->get());
	}

	abstract public function persist($beAtomic = true);
	abstract protected function load();

	protected function getTableName()
	{
		$conventional = $this->getFirstRepository()->getMapper()->getConventional();
		return $conventional->getManyToManyTableName($this->getFirstRepository(), $this->getSecondRepository());
	}
	protected function getFirstParamName()
	{
		$conventional = $this->getFirstRepository()->getMapper()->getConventional();
		return $conventional->foreignKeyFormat('first');
	}
	protected function getSecondParamName()
	{
		$conventional = $this->getFirstRepository()->getMapper()->getConventional();
		return $conventional->foreignKeyFormat('second');
	}
	final protected function getParentParamName()
	{
		return $this->parentIsFirst ? $this->getFirstParamName() : $this->getSecondParamName();
	}
	final protected function getChildParamName()
	{
		return $this->parentIsFirst ? $this->getSecondParamName() : $this->getFirstParamName();
	}
}


abstract class DibiManyToMany extends ManyToMany
{
	public function persist($beAtomic = true)
	{
		$connection = $this->getFirstRepository()->getMapper()->getConnection();

		$childsId = array();
		foreach ($this->get() as $entity)
		{
			$this->childRepository->persist($entity, $beAtomic);
			$childsId[] = $entity->id;
		}

		$parentId = $this->parent->id;

		$table = $this->getTableName();
		$paramParent = $this->getParentParamName();
		$paramChild = $this->getChildParamName();

		$connection->delete($table)
			->where('%n = %i AND %n NOT IN %in',
				$paramParent, $parentId,
				$paramChild, $childsId ? $childsId : array(0)
			)->execute()
		;

		if ($childsId)
		{
			$unexistChildsId = array_diff($childsId,
				$connection->select($paramChild)
					->from($table)
					->where('%n = %i AND %n IN %in',
						$paramParent, $parentId,
						$paramChild, $childsId
					)->fetchPairs()
			);

			if ($unexistChildsId)
			{
				foreach ($unexistChildsId as $childId)
				{
					$connection->insert($table, array(
						$paramParent => $parentId,
						$paramChild => $childId,
					))->execute();
				}
			}
		}

	}

	protected function load()
	{
		if (!isset($this->parent->id)) return array();
		$connection = $this->getFirstRepository()->getMapper()->getConnection();
		$table = $this->getTableName();
		$paramParent = $this->getParentParamName();
		$paramChild = $this->getChildParamName();

		return $connection->select($paramChild)
			->from($table)
			->where('%n = %i',
				$paramParent, $this->parent->id
			)->fetchPairs()
		;
	}
}

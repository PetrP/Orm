<?php

require_once dirname(__FILE__) . '/IRelationship.php';

// todo moznost mit parametry/ data
// ->add($entity, array('time' => time()))
// todo rict entity ze se zmenila
abstract class ManyToMany extends Object implements IteratorAggregate, Countable, IRelationship
{
	protected $parentRepository;
	protected $childRepository;

	protected $parent;
	protected $childs;
	protected $get;

	protected $parentIsFirst;

	public function __construct(IEntity $entity)
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
		return Model::get()->getRepository(substr(get_class($this), 0, strpos(get_class($this), 'To')));
	}

	/** @return Repository */
	protected function getSecondRepository()
	{
		return Model::get()->getRepository(substr(get_class($this), strpos(get_class($this), 'To') + 2));
	}

	final public function add($entity)
	{
		$data = NULL;
		if (is_array($entity))
		{
			$data = $entity;
			if (isset($data['id']))
			{
				$entity = $this->childRepository->getById($data['id']);
			}
			if (!$entity)
			{
				$entityClass = $this->childRepository->getEntityClassName($data);
				$entity = new $entityClass;
			}
			$entity->setValues($data);
		}
		else if (!($entity instanceof IEntity))
		{
			$entity = $this->childRepository->getById($entity);
		}


		if ($this->ignore($entity, $data))
		{
			return $this;
		}

		if (!$this->childRepository->isEntity($entity))
		{
			throw new UnexpectedValueException();
		}

		if ($this->childs === NULL)
		{
			$this->childs = $this->get()->fetchAll();
		}

		$this->childs[] = $entity;
		return $this;
	}

	final public function get()
	{
		if ($this->childs !== NULL)
		{
			$this->get = new ArrayDataSource($this->childs);
			$this->childs = NULL;
		}
		else if ($this->get === NULL)
		{
			$data = $this->load();
			$this->get = $data ? $this->childRepository->findById($data) : new ArrayDataSource(array());
		}
		return $this->get;
	}

	final public function set(array $data)
	{
		$this->childs = array();
		array_map(array($this, 'add'), $data);
		return $this;
	}

	final public function getIterator()
	{
		return new ArrayIterator($this->get());
	}

	abstract public function persist();
	abstract protected function load();

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

	public function count()
	{
		return iterator_count($this->getIterator());
	}

	protected function ignore(IEntity $entity, array $data = NULL)
	{
		return false;
	}
}


abstract class DibiManyToMany extends ManyToMany
{
	protected function getTableName()
	{
		$conventional = $this->getFirstRepository()->getMapper()->getConventional();
		return $conventional->getManyToManyTableName($this->getFirstRepository(), $this->getSecondRepository());
	}

	public function persist()
	{
		$connection = $this->getFirstRepository()->getMapper()->getConnection();

		$this->getFirstRepository()->getMapper()->begin();

		$childsId = array();
		foreach ($this->get() as $entity)
		{
			$this->childRepository->persist($entity);
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

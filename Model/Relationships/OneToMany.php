<?php

require_once dirname(__FILE__) . '/IRelationship.php';

class OneToMany extends Object implements IteratorAggregate, Countable, IRelationship
{
	/** @var Entity */
	private $parent;

	/** @return string */
	protected function getFirstEntityName()
	{
		return substr(get_class($this), 0, strpos(get_class($this), 'To'));
	}

	/** @return string */
	protected function getSecondParamName()
	{
		$param =  $this->getFirstEntityName();;
		if ($param{0} != '_') $param{0} = $param{0} | "\x20";
		return $param;
	}

	/** @return Repository */
	protected function getSecondRepository()
	{
		return Model::get()->getRepository(substr(get_class($this), strpos(get_class($this), 'To') + 2));
	}

	/** @param IEntity $parent */
	public function __construct(IEntity $parent)
	{
		$entityName = $this->getFirstEntityName();
		if (!($parent instanceof $entityName))
		{
			throw new Exception();
		}
		$this->parent = $parent;
	}

	/** @return int */
	public function count()
	{
		return iterator_count($this->getIterator());
	}

	/** @return Traversable */
	public function getIterator()
	{
		return $this->get()->getIterator();
	}

	/** @return IEntityCollection */
	final public function get()
	{
		if (!isset($this->get))
		{
			$param = $this->getSecondParamName();
			$this->get = $this->getSecondRepository()->{'findBy' . $param}($this->parent);
		}
		return $this->get;
	}

	/**
	 * @param array
	 * @param object
	 * @return IEntity|NULL
	 */
	protected function compare(& $all, $row)
	{
		if (isset($row['id']) AND isset($all[$row['id']]))
		{
			$entity = $all[$row['id']];
			unset($all[$row['id']]);
			return $entity;
		}
	}

	protected function row($row)
	{
		return $row;
	}

	protected function prepareAllForSet()
	{
		return $this->get()->fetchAssoc('id');
	}

	public function set(array $data)
	{
		$all = $this->prepareAllForSet();
		$repository = $this->getSecondRepository();
		$param = $this->getSecondParamName();
		$result = array();

		$order = 0;
		foreach (array_values($data) as $row)
		{
			$row = $this->row($row);
			if ($row === NULL)
			{
				continue;
			}
			else if ($row instanceof IEntity) // todo original entitu zahodin, to neni dobre
			{
				$row = $row->toArray();
			}
			else if (!is_array($row))
			{
				throw new Exception();
			}

			$entity = $this->compare($all, $row);
			if (!$entity)
			{
				$en = $repository->getEntityClassName($row);
				$entity = new $en;
			}
			$entity->setValues($row);
			if ($entity->hasParam('order')) $entity->order = ++$order;
			$entity->$param = $this->parent;
			$result[] = $entity;
		}


		$this->get = new ArrayDataSource($result);

		$this->_del($all, $param);
	}

	private function _del($all, $param)
	{
		foreach ($all as $entity)
		{
			if (is_array($entity)) $this->_del($entity, $param);
			else
			{
				try {
					$entity->$param = NULL;
				} catch (Exception $e) {
					$this->del[] = $entity;
					// todo zvlastni chovani, kdyz nemuze existovat bez param tak se vymaze
				}
			}
		}
	}

	private $del = array();
	private $get;

	public function persist()
	{
		$repository = $this->getSecondRepository();
		foreach ($this->del as $entity)
		{
			$repository->remove($entity);
		}

		$repository = $this->getSecondRepository();
		foreach ($this->get() as $entity)
		{
			$repository->persist($entity);
		}
	}

}

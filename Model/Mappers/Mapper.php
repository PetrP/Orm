<?php

require_once dirname(__FILE__) . '/IMapper.php';

require_once dirname(__FILE__) . '/Conventional/NoConventional.php';


abstract class Mapper extends Object implements IMapper
{
	abstract public function findAll();
	abstract protected function findBy(array $where);
	abstract protected function getBy(array $where);
	abstract public function persist(Entity $entity);
	abstract public function delete($entity);
	abstract public function begin(); // todo rename?
	abstract public function flush();

	protected $repository;

	private $conventional;

	public function __construct(Repository $repository)
	{
		$this->repository = $repository;
	}

	public function getRepository()
	{
		return $this->repository;
	}

	public function getConventional()
	{
		if (!isset($this->conventional))
		{
			$conventional = $this->createConventional();
			if (!($conventional instanceof IConventional))
			{
				throw new InvalidStateException();
			}
			$this->conventional = $conventional;
		}
		return $this->conventional;
	}

	protected function createConventional()
	{
		return new NoConventional($this);
	}

	public function __call($name, $args)
	{
		try {
			return parent::__call($name, $args);
		} catch (MemberAccessException $e) {

			$mode = $by = NULL;
			if (substr($name, 0, 6) === 'findBy')
			{
				$mode = 'find';
				$by = substr($name, 6);
			}
			else if (substr($name, 0, 5) === 'getBy')
			{
				$mode = 'get';
				$by = substr($name, 5);
			}

			if ($mode AND $by)
			{
				$where = array();
				// todo prvni na male pismeno udelat rychleji
				foreach (array_map(create_function('$v', 'if ($v{0} != "_") $v{0} = $v{0} | "\x20"; return $v;'),explode('And', $by)) as $n => $key) // lcfirst
				{
					if (!array_key_exists($n, $args)) throw new InvalidArgumentException("There is no value for '$key'.");
					$where[$key] = $args[$n];
				}
				return $mode === 'get' ? $this->getBy($where) : $this->findBy($where);
			}

			throw $e;
		}
	}



}

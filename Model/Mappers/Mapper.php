<?php

require_once dirname(__FILE__) . '/IMapper.php';

require_once dirname(__FILE__) . '/Conventional/NoConventional.php';


abstract class Mapper extends Object implements IMapper
{
	abstract public function findAll();
	abstract public function persist(IEntity $entity);
	abstract public function delete(IEntity $entity);
	abstract public function begin(); // todo rename?
	abstract public function flush();

	protected $repository;

	private $conventional;

	public function __construct(IRepository $repository)
	{
		$this->repository = $repository;
	}

	public function getRepository()
	{
		return $this->repository;
	}

	final public function getModel()
	{
		return $this->repository->getModel();
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
			if (substr($name, 0, 6) === 'findBy' OR substr($name, 0, 5) === 'getBy')
			{
				return call_user_func_array(array($this->findAll(), $name), $args);
			}
			throw $e;
		}
	}



}

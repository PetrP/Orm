<?php

abstract class Mapper extends Object implements IMapper
{
	abstract public function findAll();
	
	public function getConnection()
	{
		return dibi::getConnection();
	}
	
	protected $repository;
	
	public function __construct(Repository $repository)
	{
		$this->repository = $repository;
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
				$all = $this->findAll()->getSource();
				// todo instanceof DibiDataSource
				foreach (array_map('ucfirst',explode('And', $by)) as $n => $key)
				{
					if (!isset($args[$n])) throw new InvalidArgumentException("There is no '$key' value;");
					$all->where('%n = %s', $key, $args[$n]);
				}
				if ($mode === 'get')
				{
					return $this->apply($all->setRowClass('a')->fetch());
				}
				else
				{
					return $this->apply($all);
				}
				
			}
		
		
			throw $e;
		}
	}
	
	protected function apply($data)
	{
		if ($data instanceof a)
		{
			return $this->repository->createEntity($data);
		}
		else
		{
			if (!($data instanceof DibiResult))
			{
				return $data->getResult();
			}
			return new EntityCollection($this->repository, $data->setRowClass('a'));
		}
	}
	
	
}
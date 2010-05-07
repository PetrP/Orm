<?php

abstract class Mapper extends Object implements IMapper
{
	abstract public function findAll();
	
	public function __call($name, $args)
	{
		try {
			return parnt::__call($name, $args);
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
				$all = $this->findAll();
				// todo instanceof DibiDataSource
				foreach (array_map('ucfirst',explode('And', $by)) as $n => $key)
				{
					if (!isset($args[$n])) throw new InvalidArgumentException("There is no '$key' value;");
					$all->where('%n = %s', $key, $args[$n]);
				}
				
				return $all;
			}
		
		
			throw $e;
		}
	}
	
	
}
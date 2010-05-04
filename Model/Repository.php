<?php

abstract class Repository extends Object implements IRepository
{
	private $mapper;
	
	protected function getMapper()
	{
		if (!isset($this->mapper))
		{
			$this->mapper = $this->createMapper();
		}
		return $this->mapper;
	}
	
	protected function createMapper()
	{
		$class = Factory::getMapperClass($this);
		
		return new $class;
	}
	
}



<?php

class Model extends Object
{
	private static $repositories = array();
	
	public static function getRepository($name)
	{
		$name = strtolower($name);
		if (!isset(self::$repositories[$name]))
		{
			$class = $name . 'Repository';
			$class[0] = strtoupper($class[0]);
			
			$r = new $class($name);
			if (!($r instanceof Repository))
			{
				throw new InvalidStateException();
			}
			self::$repositories[$name] = $r;
		}
		return self::$repositories[$name];
	}
	
}

class a
{
	public $d;
	public function __construct($d)
	{
		$this->d = $d;
	}
}

class EntityCollection extends SmartCachingIterator
{
	private $repository;
	
	private $source;
	
	public function __construct(Repository $repository, $source)
	{
		$this->repository = $repository;
		parent::__construct($this->source = $source);
	}
	
	public function current()
	{
		return $this->repository->createEntity(parent::current());
	}
	
	public function getSource()
	{
		return $this->source;
	}
	
	public function __toString()
	{
		try {
			return parent::__toString();
		} catch (Exception $e) {
			Debug::toStringException($e);
		}
	}
	
}


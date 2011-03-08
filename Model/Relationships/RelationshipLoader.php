<?php

class RelationshipLoader extends Object
{
	private $class;
	private $name;

	public function __construct($type, $className)
	{
		if (!strpos($className, 'To')) throw new Exception(); // todo
		$this->name = $className;

		if (class_exists($className))
		{
			$parents = class_parents($className);
			if (!isset($parents[$type === MetaData::ManyToMany ? 'ManyToMany' : 'OneToMany'])) throw new InvalidStateException();
			$this->class = $className;
		}
		else
		{
			$this->class = $type === MetaData::ManyToMany ? 'ManyToMany' : 'OneToMany';
		}
	}

	public function create($className, IEntity $parent, $value = NULL)
	{
		if ($this->class !== $className) throw new Exception();
		return call_user_func(array($className, 'create'), $className, $parent, $value, $this->name);
	}

}

<?php

interface IEntityInjection
{
	function getInjectedValue();

	function setInjectedValue($value);

	static function create($className, IEntity $entity, $value = NULL);
}

interface IEntityInjectionArray extends IEntityInjection // todo?
{
}

abstract class Injection extends Object implements IEntityInjection
{
	protected $value;

	public static function create($className, IEntity $entity, $value = NULL)
	{
		if (method_exists($className, '__construct'))
		{
			$construct = new MethodReflection($className, '__construct');
			if ($construct->getNumberOfRequiredParameters())
			{
				throw new Exception("$className has required parameters in constructor, use custom factory");
			}
		}
		$injection = new $className;
		if (!($injection instanceof self))
		{
			throw new Exception();
		}
		return $injection;
	}

	public function getInjectedValue()
	{
		return $this->value;
	}

	public function setInjectedValue($value)
	{
		$this->value = $value;
	}
}


class InjectionFactory
{
	private $className;
	private $callback;

	private function __construct() {}

	public static function create(Callback $callback, $className)
	{
		$factory = new self;
		$factory->callback = $callback->getNative();
		$factory->className = $className;
		return callback($factory, 'call');
	}

	public function call(IEntity $entity, $value = NULL)
	{
		return call_user_func($this->callback, $this->className, $entity, $value);
	}

}

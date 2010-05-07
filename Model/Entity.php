<?php

abstract class Entity extends Object implements IEntity
{
	private $params = array();
	
	final public function & __get($name)
	{
		$params = Manager::getEntityParams(get_class($this));
		
		if (!isset($params[$name]))
		{
			$tmp = parent::__get($name);
			return $tmp;
		}
		else if (!isset($params[$name]['get']))
		{
			throw new MemberAccessException("Cannot assign to a write-only property ".get_class($this)."::\$$name.");
		}
		
		$value = NULL;
		if ($params[$name]['get']['method'])
		{
			$value = callback($this, $params[$name]['get']['method'])->invoke($value);
		}
		else
		{
			$value = $this->getValue($name);
		}
		
		return $value;
	}
	
	final public function __set($name, $value)
	{
		$params = Manager::getEntityParams(get_class($this));
		
		if (!isset($params[$name]))
		{
			return parent::__set($name, $value);
		}
		else if (!isset($params[$name]['set']))
		{
			throw new MemberAccessException("Cannot assign to a read-only property ".get_class($this)."::\$$name.");
		}
		
		if ($params[$name]['set']['method'])
		{
			callback($this, $params[$name]['set']['method'])->invoke($value);
		}
		else
		{
			$this->setValue($name, $value);
		}
		
		return $this;
	}
	
	final public function __call($name, $args)
	{
		$m = substr($name, 0, 3);
		if ($m === 'get' OR ($m === 'set' AND array_key_exists(0, $args)))
		{
			$var = substr($name, 3);
			$var{0} = strtolower($var{0});
			
			$params = Manager::getEntityParams(get_class($this));
			if (isset($params[$var]))
			{
				return callback($this, '__' . $m)->invoke($var, $m === 'set' ? $args[0] : NULL);
			}
		}
		
		return parent::__call($name, $args);
	}
	
	final protected function getValue($name, $need = true)
	{
		$params = Manager::getEntityParams(get_class($this));
		
		if (!isset($params[$name]))
		{
			throw new MemberAccessException("Cannot read an undeclared property ".get_class($this)."::\$$name.");
		}
		else if (!isset($params[$name]['get']))
		{
			throw new MemberAccessException("Cannot assign to a write-only property ".get_class($this)."::\$$name.");
		}
		
		$value = NULL;
		if (isset($this->params[$name]))
		{
			$value = $this->params[$name];
		}
		
		if (!Manager::isParamValid($params[$name]['get']['type'], $value))
		{
			if ($need)
			{
				$type = $params[$name]['get']['type'];
				throw new InvalidStateException("Param $name must be '$type', " . (is_object($value) ? get_class($value) : gettype($value)) . " given");
			}
			else
			{
				return NULL;
			}
		}
		
		return $value;
	}
	
	
	final protected function setValue($name, $value)
	{
		$params = Manager::getEntityParams(get_class($this));
		
		if (!isset($params[$name]))
		{
			throw new MemberAccessException("Cannot assign to an undeclared property ".get_class($this)."::\$$name.");
		}
		else if (!isset($params[$name]['set']))
		{
			throw new MemberAccessException("Cannot assign to a read-only property ".get_class($this)."::\$$name.");
		}
		
		if (!Manager::isParamValid($params[$name]['set']['type'], $value))
		{
			$type = $params[$name]['set']['type'];
			throw new InvalidStateException("Param $name must be '$type', " . (is_object($value) ? get_class($value) : gettype($value)) . " given");
		}
		
		$this->params[$name] = $value;
		
		return $this;
	}
	
	
	public function compare(Entity $e)
	{
		if ($e === $this) return true;
		return false;
	}
	
}
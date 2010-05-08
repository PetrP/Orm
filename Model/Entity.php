<?php

abstract class EntityArrayObject extends ArrayObject
{
	/**
	 * Access to reflection.
	 *
	 * @return ClassReflection
	 */
	public function getReflection()
	{
		return new ClassReflection($this);
	}



	/**
	 * Call to undefined method.
	 *
	 * @param  string  method name
	 * @param  array   arguments
	 * @return mixed
	 * @throws MemberAccessException
	 */
	public function __call($name, $args)
	{
		return ObjectMixin::call($this, $name, $args);
	}



	/**
	 * Call to undefined static method.
	 *
	 * @param  string  method name (in lower case!)
	 * @param  array   arguments
	 * @return mixed
	 * @throws MemberAccessException
	 */
	public static function __callStatic($name, $args)
	{
		$class = get_called_class();
		throw new MemberAccessException("Call to undefined static method $class::$name().");
	}



	/**
	 * Adding method to class.
	 *
	 * @param  string  method name
	 * @param  mixed   callback or closure
	 * @return mixed
	 */
	public static function extensionMethod($name, $callback = NULL)
	{
		if (strpos($name, '::') === FALSE) {
			$class = get_called_class();
		} else {
			list($class, $name) = explode('::', $name);
		}
		$class = new ClassReflection($class);
		if ($callback === NULL) {
			return $class->getExtensionMethod($name);
		} else {
			$class->setExtensionMethod($name, $callback);
		}
	}



	/**
	 * Returns property value. Do not call directly.
	 *
	 * @param  string  property name
	 * @return mixed   property value
	 * @throws MemberAccessException if the property is not defined.
	 */
	public function &__get($name)
	{
		return ObjectMixin::get($this, $name);
	}



	/**
	 * Sets value of a property. Do not call directly.
	 *
	 * @param  string  property name
	 * @param  mixed   property value
	 * @return void
	 * @throws MemberAccessException if the property is not defined or is read-only
	 */
	public function __set($name, $value)
	{
		return ObjectMixin::set($this, $name, $value);
	}



	/**
	 * Is property defined?
	 *
	 * @param  string  property name
	 * @return bool
	 */
	public function __isset($name)
	{
		return ObjectMixin::has($this, $name);
	}



	/**
	 * Access to undeclared property.
	 *
	 * @param  string  property name
	 * @return void
	 * @throws MemberAccessException
	 */
	public function __unset($name)
	{
		throw new MemberAccessException("Cannot unset the property {$this->reflection->name}::\$$name.");
	}
	
	
	
	public function __construct()
	{
		
	}
	public function offsetExists($name)
	{
		return $this->__isset($name);
	}
	public function offsetGet($name)
	{
		return $this->__get($name);
	}
	public function offsetSet($name, $value)
	{
		return $this->__set($name, $value);
	}
	
	
	public function offsetUnset($name)
	{
		throw new NotSupportedException();
	}
	public function append($value)
	{
		throw new NotSupportedException();
	}
	public function getArrayCopy()
	{
		throw new NotSupportedException();
	}
	public function count()
	{
		throw new NotSupportedException();
	}
	public function getFlags()
	{
		throw new NotSupportedException();
	}
	public function setFlags($value)
	{
		throw new NotSupportedException();
	}
	public function asort()
	{
		throw new NotSupportedException();
	}
	public function ksort()
	{
		throw new NotSupportedException();
	}
	public function uasort($cmp)
	{
		throw new NotSupportedException();
	}
	public function uksort($cmp)
	{
		throw new NotSupportedException();
	}
	public function natsort()
	{
		throw new NotSupportedException();
	}
	public function natcasesort()
	{
		throw new NotSupportedException();
	}
	public function unserialize($serialized)
	{
		throw new NotSupportedException();
	}
	public function serialize()
	{
		throw new NotSupportedException();
	}
	public function getIterator()
	{
		throw new NotSupportedException();
	}
	public function exchangeArray($input)
	{
		throw new NotSupportedException();
	}
	public function setIteratorClass($class)
	{
		throw new NotSupportedException();
	}
	public function getIteratorClass()
	{
		throw new NotSupportedException();
	}
}


abstract class Entity extends Object implements IEntity, ArrayAccess
{
	private $values = array();
	
	private $rules;
	
	public function __construct()
	{
		$this->rules = $this->getEntityRules();
	}
	
	final public function & __get($name)
	{
		$rules = $params = $this->rules;
		
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
		$rules = $params = $this->rules;
		
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
	
	final public function __isset($name)
	{
		$rules = $params = $this->rules;
		
		if (!isset($params[$name]))
		{
			return parent::__isset($name);
		}
		else if (isset($params[$name]['get']))
		{
			try {
				return $this->__get($name) !== NULL;
			} catch (Exception $e) {
				return false;
			}
		}
		
		return false;
		

	}
	
	final protected function getValue($name, $need = true)
	{
		$rules = $params = $this->rules;
		
		if (!isset($params[$name]))
		{
			throw new MemberAccessException("Cannot read an undeclared property ".get_class($this)."::\$$name.");
		}
		else if (!isset($params[$name]['get']))
		{
			throw new MemberAccessException("Cannot assign to a write-only property ".get_class($this)."::\$$name.");
		}
		
		$value = NULL;
		$valid = false;
		if (isset($this->values[$name]))
		{
			$value = $this->values[$name][1];
			$valid = $this->values[$name][0];
		}
		
		if (!$valid)
		{
			if (isset($params[$name]['set']))
			{
				try {
					$this->__set($name, $value);
					
				} catch (UnexpectedValueException $e) {
					if ($need) throw $e;
					return NULL;
				}
				$value = isset($this->values[$name]) ? $this->values[$name][1] : NULL; // todo kdyz neni nastaveno muze to znamenat neco spatne, vyhodit chybu?
			}
			else
			{
				if (!Manager::isParamValid($params[$name]['types'], $value))
				{
					if ($need)
					{
						$type = $params[$name]['types'];
						throw new UnexpectedValueException("Param $name must be '$type', " . (is_object($value) ? get_class($value) : gettype($value)) . " have");
					}
					return NULL;
				}
				$this->values[$name] = array(true, $value);
			}
		}
		
		return $value;
	}
	
	
	final protected function setValue($name, $value)
	{
		$rules = $params = $this->rules;
		
		if (!isset($params[$name]))
		{
			throw new MemberAccessException("Cannot assign to an undeclared property ".get_class($this)."::\$$name.");
		}
		else if (!isset($params[$name]['set']))
		{
			throw new MemberAccessException("Cannot assign to a read-only property ".get_class($this)."::\$$name.");
		}
		
		if (!Manager::isParamValid($params[$name]['types'], $value))
		{
			$type = $params[$name]['types'];
			throw new UnexpectedValueException("Param $name must be '$type', " . (is_object($value) ? get_class($value) : gettype($value)) . " given");
		}
		
		$this->values[$name] = array(true, $value);
		
		return $this;
	}
	
	/**
	 * @internal
	 */
	final public static function create($entityName, array $data)
	{
		$entity = new $entityName;
		if (!($entity instanceof self)) throw new InvalidStateException();
		self::setPrivateValues($entity, $data);
		return $entity;
	}
	
	/**
	 * @internal
	 */
	final public static function setPrivateValues(Entity $entity, $values)
	{
		$rules = $params = $entity->rules;

		foreach ($values as $name => $value)
		{
			if (isset($params[$name]))
			{
				$entity->values[$name] = array(0 => false, 1 => $value);
			}
			else
			{
				throw new InvalidArgumentException($name); // todo ?
			}
			
		}
		
	}
	/**
	 * @internal
	 */
	final public static function getPrivateValues(Entity $entity)
	{
		$rules = $params = $entity->rules;
		$values = array();
		
		if ($entity->__isset('id'))
		{
			$values['id'] = $entity->__get('id');
		}
		
		foreach ($params as $name => $rule)
		{
			if ($name === 'id') continue;
			
			if (isset($params[$name]['get']))
			{
				$values[$name] = $entity->__get($name);
			}
			else if (isset($params[$name]['set']))
			{
				$value = isset($entity->values[$name]) ? $entity->values[$name][1] : NULL;
				if (!isset($entity->values[$name]) OR !$entity->values[$name][0])
				{
					$entity->__set($name, $value);
					$value = isset($entity->values[$name]) ? $entity->values[$name][1] : NULL; // todo kdyz neni nastaveno muze to znamenat neco spatne, vyhodit chybu?
				}
				$values[$name] = $value;
			}
			else
			{
				throw new MemberAccessException(); // todo ?
			}
		}
		
		return $values;
	}

	public function offsetExists($name)
	{
		return $this->__isset($name);
	}
	public function offsetGet($name)
	{
		return $this->__get($name);
	}
	public function offsetSet($name, $value)
	{
		return $this->__set($name, $value);
	}
	public function offsetUnset($name)
	{
		throw new NotSupportedException();
	}
	
	public function toArray()
	{
		$rules = $params = $this->rules;
		$result = array();
		
		if ($this->__isset('id'))
		{
			$result['id'] = $this->__get('id');
		}
		
		foreach ($params as $name => $rule)
		{
			if ($name === 'id') continue;
			
			if (isset($params[$name]['get']))
			{
				$result[$name] = $this->__get($name);
			}
		}
		
		return $result;
	}
	
	final private function getEntityRules()
	{
		return Manager::getEntityParams(get_class($this));
	}
	
}
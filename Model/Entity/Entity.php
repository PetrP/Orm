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

/**
 * @property-read int $id
 */
abstract class Entity extends Object implements IEntity, ArrayAccess, IteratorAggregate
{
	private $values = array();

	private $valid = array();

	private $rules;


	public function __construct()
	{
		$this->rules = $this->getEntityRules(get_class($this));
	}

	protected function check()
	{

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
			$value = $this->{$params[$name]['get']['method']}($value); // todo mohlo by zavolat private metodu, je potreba aby vse bylo final
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
			$this->{$params[$name]['set']['method']}($value); // todo mohlo by zavolat private metodu, je potreba aby vse bylo final
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

			$params = EntityManager::getEntityParams(get_class($this));
			if (isset($params[$var]))
			{
				return $this->{'__' . $m}($var, $m === 'set' ? $args[0] : NULL);
			}
		}

		return parent::__call($name, $args);
	}

	final public function __isset($name)
	{
		$rules = $this->rules;

		if (!isset($rules[$name]))
		{
			return parent::__isset($name);
		}
		else if (isset($rules[$name]['get']))
		{
			try {
				return $this->__get($name) !== NULL;
			} catch (Exception $e) {
				return false;
			}
		}

		return false;
	}
	const EXISTS = NULL;
	const READ = 'r';
	const WRITE = 'w';
	const READWRITE = 'rw';
	final public function hasParam($name, $mode = self::EXISTS)
	{
		if ($mode === self::EXISTS)
		{
			return isset($this->rules[$name]);
		}
		else if ($mode === self::READWRITE)
		{
			return isset($this->rules[$name]['get']) AND isset($this->rules[$name]['set']);
		}
		else if ($mode === self::READ OR $mode === self::WRITE)
		{
			return $mode === self::READ ? isset($this->rules[$name]['get']) : isset($this->rules[$name]['set']);
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
		if (array_key_exists($name, $this->values))
		{
			$valid = isset($this->valid[$name]) ? $this->valid[$name] : false;
			$value = $this->values[$name];
		}
		// todo povolit mit ho i jako id rovnou v $name a to __fk__id zrusit
		else if (isset($params[$name]['fk']) AND array_key_exists($fk = $name . '__fk__id', $this->values))
		{
			$value = Model::getRepository($params[$name]['fk'])->getById($this->values[$fk]);
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
				$value = isset($this->values[$name]) ? $this->values[$name] : NULL; // todo kdyz neni nastaveno muze to znamenat neco spatne, vyhodit chybu?
			}
			else
			{
				if (!ValidationHelper::isValid($params[$name]['types'], $value))
				{
					if ($need)
					{
						$type = implode('|',$params[$name]['types']);
						throw new UnexpectedValueException("Param $name must be '$type', " . (is_object($value) ? get_class($value) : gettype($value)) . " have");
					}
					return NULL;
				}
				$this->values[$name] = $value;
				$this->valid[$name] = true;
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

		if (isset($params[$name]['fk']) AND !($value instanceof Entity))
		{
			$id = (string) $value;
			if ($id)
			{
				$value = Model::getRepository($params[$name]['fk'])->getById($id);
			}
		}

		if (!ValidationHelper::isValid($params[$name]['types'], $value))
		{
			$type = implode('|',$params[$name]['types']);
			throw new UnexpectedValueException("Param $name must be '$type', " . (is_object($value) ? get_class($value) : gettype($value)) . " given");
		}

		$this->values[$name] = $value;
		$this->valid[$name] = true;

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
	final public static function setPrivateValues(Entity $entity, array $values)
	{
		if (!$entity->values)
		{
			$entity->values = $values;
			$entity->valid = array();
		}
		else
		{
			foreach ($values as $name => $value)
			{
				$entity->values[$name] = $value;
				$entity->valid[$name] = false;
			}
		}
	}
	/**
	 * @internal
	 */
	final public static function getPrivateValues(Entity $entity)
	{
		$entity->check();

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
				$value = isset($entity->values[$name]) ? $entity->values[$name] : NULL;
				if (!isset($entity->valid[$name]) OR !$entity->valid[$name])
				{
					$entity->__set($name, $value);
					$value = isset($entity->values[$name]) ? $entity->values[$name] : NULL; // todo kdyz neni nastaveno muze to znamenat neco spatne, vyhodit chybu?
				}
				$values[$name] = $value;
			}
			else
			{
				throw new MemberAccessException(); // todo ?
			}
			/*if (isset($rule['fk']))
			{
				$entity = $values[$name];
				if (!($entity instanceof Entity)) throw new Exception();
				if (!isset($entity->id)) throw new Exception();
				$values[$name.'_id'] = $entity->id; // todo contention
				unset($values[$name]);
			}*/
		}

		return $values;
	}

	final public static function getFk($entityName)
	{
		$result = array();
		foreach (self::getEntityRules($entityName) as $name => $rule)
		{
			if (!isset($rule['fk'])) continue;
			$result[$name] = $rule['fk'];
		}
		return $result;
	}

	final public function offsetExists($name)
	{
		return $this->__isset($name);
	}
	final public function offsetGet($name)
	{
		return $this->__get($name);
	}
	final public function offsetSet($name, $value)
	{
		return $this->__set($name, $value);
	}
	final public function offsetUnset($name)
	{
		throw new NotSupportedException();
	}

	final public function toArray()
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

	final public function toPlainArray()
	{
		$result = $this->toArray();
		foreach ($result as $name => $value)
		{
			if ($value instanceof Entity)
			{
				$result[$name] = $value->id;
			}
		}
		return $result;
	}

	final public function setValues($values)
	{
		foreach ($values as $name => $value)
		{
			// todo nepokusit se zapsat i nezname?
			if ($this->hasParam($name, self::WRITE))
			{
				$this->__set($name, $value);
			}
		}
	}

	final public function getIterator()
	{
		return new ArrayIterator($this->toArray());
	}

	final private static function getEntityRules($entityClass)
	{
		return EntityManager::getEntityParams($entityClass);
	}

	public function __toString()
	{
		try {
			// mozna zrusit
			return isset($this->id) ? (string) $this->id : NULL;
		} catch (Exception $e) {
			Debug::toStringException($e);
		}
	}

	public function __clone()
	{
		$this->valid['id'] = false;
		$this->values['id'] = NULL;
	}

}
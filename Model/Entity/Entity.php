<?php

require_once dirname(__FILE__) . '/IEntity.php';

require_once dirname(__FILE__) . '/EntityManager.php';

require_once dirname(__FILE__) . '/ValidationHelper.php';


/**
 * @property-read int $id
 */
abstract class Entity extends Object implements IEntity, ArrayAccess, IteratorAggregate
{
	private $values = array();

	private $valid = array();

	private $rules;

	private $repositoryName;

	private $changed = true;

	final public function isChanged()
	{
		return isset($this->id) ? $this->changed : true;
	}

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
			$value = $this->{$params[$name]['get']['method']}(); // todo mohlo by zavolat private metodu, je potreba aby vse bylo final
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
		else if ($this->getGeneratingRepository(false)) // lazy load
		{
			if ($lazyLoadParams = $this->getGeneratingRepository()->lazyLoad($this, $name))
			{
				$this->setPrivateValues($this, $lazyLoadParams);
				if (array_key_exists($name, $this->values))
				{
					$value = $this->values[$name];
				}
			}
		}


		if (!$valid)
		{
			if (isset($params[$name]['set']))
			{
				$tmpChanged = $this->changed;
				try {
					$this->__set($name, $value);

				} catch (UnexpectedValueException $e) {
					$this->changed = $tmpChanged;
					if ($need) throw $e;
					return NULL;
				}
				$this->changed = $tmpChanged;
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

		$this->changed = true;

		$this->values[$name] = $value;
		$this->valid[$name] = true;

		return $this;
	}

	/**
	 * @internal
	 */
	final public static function create($entityName, array $data, Repository $repository)
	{
		$entity = new $entityName;
		if (!($entity instanceof self)) throw new InvalidStateException();
		$entity->repositoryName = $repository->getRepositoryName();
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
					$tmpChanged = $entity->changed;
					$entity->__set($name, $value);
					$entity->changed = $tmpChanged;
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

	final public function getGeneratingRepository($need = true)
	{
		if (!$this->repositoryName)
		{
			if ($need)
			{
				throw new InvalidStateException();
			}
			else
			{
				return NULL;
			}
		}
		return Model::getRepository($this->repositoryName);
	}
}

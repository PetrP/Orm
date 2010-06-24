<?php

require_once dirname(__FILE__) . '/IEntity.php';

require_once dirname(__FILE__) . '/EntityManager.php';

require_once dirname(__FILE__) . '/ValidationHelper.php';


/**
 * @property-read int $id
 */
abstract class Entity extends Object implements IEntity
{
	const ENTITY_TO_ID = 'entidyToId';

	const DEFAULT_VALUE = "\0";

	private $values = array();

	private $valid = array();

	private $rules;

	private $repositoryName;

	private $changed = true;

	public function __construct()
	{
		$this->rules = $this->getEntityRules(get_class($this));
	}

	protected function check()
	{

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
	
	protected static function getEntityRules($entityClass)
	{
		return EntityManager::getEntityParams($entityClass);
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
	
	final public function toArray($mode = NULL)
	{
		$result = array(
			'id' => $this->__isset('id') ? $this->__get('id') : NULL,
		);

		foreach ($this->rules as $name => $rule)
		{
			if ($name === 'id') continue;

			if (isset($rule['get']))
			{
				$result[$name] = $this->__get($name);
				if ($mode === self::ENTITY_TO_ID AND isset($rule['fk']) AND $result[$name] instanceof Entity)
				{
					$result[$name] = $result[$name]->id;
				}
			}
		}

		return $result;
	}
	/** @deprecated */
	final public function toPlainArray()
	{
		return $this->toArray(self::ENTITY_TO_ID);
	}
	
	const EXISTS = NULL;
	const READ = 'r';
	const WRITE = 'w';
	const READWRITE = 'rw';
	final public function hasParam($name, $mode = self::EXISTS)
	{
		$rule = & $this->rules[$name];
		
		if ($mode === self::EXISTS)
		{
			return isset($rule);
		}
		else if ($mode === self::READWRITE)
		{
			return isset($rule['get']) AND isset($rule['set']);
		}
		else if ($mode === self::READ OR $mode === self::WRITE)
		{
			return $mode === self::READ ? isset($rule['get']) : isset($rule['set']);
		}

		return false;
	}
	
	final protected function getValue($name, $need = true)
	{
		$rule = & $this->rules[$name];

		if (!isset($rule))
		{
			throw new MemberAccessException("Cannot read an undeclared property ".get_class($this)."::\$$name.");
		}
		else if (!isset($rule['get']))
		{
			throw new MemberAccessException("Cannot read to a write-only property ".get_class($this)."::\$$name.");
		}

		$value = self::DEFAULT_VALUE;
		$valid = false;
		if (array_key_exists($name, $this->values))
		{
			$valid = isset($this->valid[$name]) ? $this->valid[$name] : false;
			$value = $this->values[$name];
		}
		// todo povolit mit ho i jako id rovnou v $name a to __fk__id zrusit
		else if (isset($rule['fk']) AND array_key_exists($fk = $name . '__fk__id', $this->values))
		{
			$value = Model::getRepository($rule['fk'])->getById($this->values[$fk]);
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
			$tmpChanged = $this->changed;
			try {
				$this->{isset($rule['set']) ? '__set' : 'setValueHelper'}($name, $value);
			} catch (UnexpectedValueException $e) {
				$this->changed = $tmpChanged;
				if ($need) throw $e;
				return NULL;
			}
			$this->changed = $tmpChanged;
			$value = isset($this->values[$name]) ? $this->values[$name] : NULL; // todo kdyz neni nastaveno muze to znamenat neco spatne, vyhodit chybu?
		}

		return $value;
	}
	
	final protected function setValue($name, $value)
	{
		$rule = & $this->rules[$name];

		if (!isset($rule))
		{
			throw new MemberAccessException("Cannot write to an undeclared property ".get_class($this)."::\$$name.");
		}
		else if (!isset($rule['set']))
		{
			throw new MemberAccessException("Cannot write to a read-only property ".get_class($this)."::\$$name.");
		}

		$this->setValueHelper($name, $value);

		return $this;
	}
	
	// todo zvazit
	final public function getGeneratingRepository($need = true)
	{
		if ($this->repositoryName) return Model::getRepository($this->repositoryName);
		else if (!$need) return NULL;
		else throw new InvalidStateException();
	}
	
	final public function isChanged()
	{
		return $this->__isset('id') ? $this->changed : true;
	}

	final public function getIterator()
	{
		return new ArrayIterator($this->toArray());
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
	
	
	

	final public function & __get($name)
	{
		$rule = & $this->rules[$name];
		
		if (!isset($rule))
		{
			$tmp = parent::__get($name);
			return $tmp;
		}
		else if (!isset($rule['get']))
		{
			throw new MemberAccessException("Cannot read to a write-only property ".get_class($this)."::\$$name.");
		}

		$value = NULL;
		if ($rule['get']['method'])
		{
			$value = $this->{$rule['get']['method']}(); // todo mohlo by zavolat private metodu, je potreba aby vse bylo final
		}
		else
		{
			$value = $this->getValue($name);
		}

		return $value;
	}

	final public function __set($name, $value)
	{
		$rule = & $this->rules[$name];

		if (!isset($rule))
		{
			return parent::__set($name, $value);
		}
		else if (!isset($rule['set']))
		{
			throw new MemberAccessException("Cannot write to a read-only property ".get_class($this)."::\$$name.");
		}

		if ($rule['set']['method'])
		{
			$this->{$rule['set']['method']}($value); // todo mohlo by zavolat private metodu, je potreba aby vse bylo final
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

			if (isset($this->rules[$var]))
			{
				return $this->{'__' . $m}($var, $m === 'set' ? $args[0] : NULL);
			}
		}

		return parent::__call($name, $args);
	}
	
	final public function __isset($name)
	{
		$rule = & $this->rules[$name];

		if (!isset($rule))
		{
			return parent::__isset($name);
		}
		else if (isset($rule['get']))
		{
			try {
				return $this->__get($name) !== NULL;
			} catch (Exception $e) {
				return false;
			}
		}

		return false;
	}
	
	
	
	
	


	


	final private function setValueHelper($name, $value)
	{
		$rule = $this->rules[$name];
		
		if ($value === self::DEFAULT_VALUE)
		{
			$default = NULL;
			if (array_key_exists('default', $rule))
			{
				$default = $rule['default'];
			}
			else
			{
				$defaultMethod = 'getDefault' . ucfirst($name);
				if (method_exists($this, $defaultMethod))
				{
					$default = $this->{$defaultMethod}();
				}
			}
			$value = $default;
		}

		if (isset($rule['fk']) AND !($value instanceof Entity))
		{
			$id = (string) $value;
			if ($id)
			{
				$value = Model::getRepository($rule['fk'])->getById($id);
			}
		}

		if (!ValidationHelper::isValid($rule['types'], $value))
		{
			$type = implode('|',$rule['types']);
			throw new UnexpectedValueException("Param $name must be '$type', " . (is_object($value) ? get_class($value) : gettype($value)) . " given");
		}
		
		$this->values[$name] = $value;
		$this->valid[$name] = true;
		$this->changed = true;
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

		$values = array();

		if ($entity->__isset('id'))
		{
			$values['id'] = $entity->__get('id');
		}

		foreach ($entity->rules as $name => $rule)
		{
			if ($name === 'id') continue;

			if (isset($rule['get']))
			{
				$values[$name] = $entity->__get($name);
			}
			else if (isset($rule['set']))
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
		}

		return $values;
	}

	// todo asi depraceted
	final public static function getFk($entityName)
	{
		$result = array();
		foreach (Entity::getEntityRules($entityName) as $name => $rule) // todo neumoznuje prepsat getEntityRules
		{
			if (!isset($rule['fk'])) continue;
			$result[$name] = $rule['fk'];
		}
		return $result;
	}
	
	
	
	
}

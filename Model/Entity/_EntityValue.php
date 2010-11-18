<?php

abstract class _EntityValue extends _EntityGeneratingRepository
{

	const DEFAULT_VALUE = "\0";

	private $values = array();

	private $valid = array();

	private $rules;

	private $changed = false;

	/** Vytvorena nova entita */
	protected function onCreate()
	{
		parent::onCreate();
		$this->changed = true;
		$this->rules = self::getEntityRules(get_class($this));
	}

	/** Vytazena z mapperu */
	protected function onLoad(IRepository $repository)
	{
		parent::onLoad($repository);
		$this->rules = self::getEntityRules(get_class($this));
	}

	/** Behem persistovani, vsechny subentity nemusi byt jeste persistovany */
	final protected function onPersist(IRepository $repository, $id)
	{
		parent::onPersist($repository, $id);
		if (!$id) throw new UnexpectedValueException();
		$this->values['id'] = $id;
		$this->valid['id'] = false;
		$this->changed = false;
	}

	/** Po vymazani */
	protected function onAfterDelete(IRepository $repository)
	{
		parent::onAfterDelete($repository);
		$this->values['id'] = NULL;
		$this->valid['id'] = false;
		$this->changed = true;
	}

	public function __clone()
	{
		$this->values['id'] = NULL;
		$this->valid['id'] = false;
		$this->changed = true;
	}

	const EXISTS = NULL;
	const READ = 'r';
	const WRITE = 'w';
	const READWRITE = 'rw';
	final public function hasParam($name, $mode = self::EXISTS)
	{
		if ($mode === self::EXISTS) return isset($this->rules[$name]);
		if (!isset($this->rules[$name])) return false;

		$rule = $this->rules[$name];
		if ($mode === self::READWRITE) return isset($rule['get']) AND isset($rule['set']);
		else if ($mode === self::READ) return isset($rule['get']);
		else if ($mode === self::WRITE) return isset($rule['set']);
		return false;
	}

	final protected function getValue($name, $need = true)
	{
		if (!isset($this->rules[$name]))
		{
			throw new MemberAccessException("Cannot read an undeclared property ".get_class($this)."::\$$name.");
		}

		$rule = $this->rules[$name];

		if (!isset($rule['get']))
		{
			throw new MemberAccessException("Cannot read to a write-only property ".get_class($this)."::\$$name.");
		}

		$value = self::DEFAULT_VALUE;
		$valid = false;
		if (isset($this->values[$name]) OR array_key_exists($name, $this->values))
		{
			$valid = isset($this->valid[$name]) ? $this->valid[$name] : false;
			$value = $this->values[$name];
		}
		else if ($this->getGeneratingRepository(false)) // lazy load
		{
			if ($lazyLoadParams = $this->getGeneratingRepository()->lazyLoad($this, $name))
			{
				foreach ($lazyLoadParams as $n => $v)
				{
					$this->values[$n] = $v;
					$this->valid[$n] = false;
				}
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
				if (isset($rule['set']))
				{
					$this->__set($name, $value);
				}
				else
				{
					$this->setValueHelper($name, $value);
				}
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
		if (!isset($this->rules[$name]))
		{
			throw new MemberAccessException("Cannot write to an undeclared property ".get_class($this)."::\$$name.");
		}

		$rule = $this->rules[$name];

		if (!isset($rule['set']))
		{
			throw new MemberAccessException("Cannot write to a read-only property ".get_class($this)."::\$$name.");
		}

		$this->setValueHelper($name, $value);

		return $this;
	}

	final public function isChanged()
	{
		return $this->__isset('id') ? $this->changed : true;
	}


	final public function & __get($name)
	{
		if (!isset($this->rules[$name]))
		{
			$tmp = parent::__get($name);
			return $tmp;
		}

		$rule = $this->rules[$name];

		if (!isset($rule['get']))
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
		if (!isset($this->rules[$name]))
		{
			return parent::__set($name, $value);
		}

		$rule = $this->rules[$name];

		if (!isset($rule['set']))
		{
			throw new MemberAccessException("Cannot write to a read-only property ".get_class($this)."::\$$name.");
		}
		if ($rule['set']['method'])
		{
			if ($value === self::DEFAULT_VALUE)
			{
				$value = $this->getDefaultValueHelper($name, $rule);
			}
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
			if ($var{0} != '_') $var{0} = $var{0} | "\x20"; // lcfirst

			if (isset($this->rules[$var]))
			{
				return $this->{'__' . $m}($var, $m === 'set' ? $args[0] : NULL);
			}
		}
		else if (substr($name, 0, 2) === 'is')
		{
			$var = substr($name, 2);
			if ($var{0} != '_') $var{0} = $var{0} | "\x20"; // lcfirst
			if (isset($this->rules[$var]) AND $this->rules[$var]['types'] === array('bool'))
			{
				return $this->__get($var);
			}
		}

		return parent::__call($name, $args);
	}

	final public function __isset($name)
	{
		if (!isset($this->rules[$name]))
		{
			return parent::__isset($name);
		}
		else if (isset($this->rules[$name]['get']))
		{
			try {
				return $this->__get($name) !== NULL;
			} catch (Exception $e) {
				return false;
			}
		}

		return false;
	}





	final private function getDefaultValueHelper($name, $rule)
	{
		$default = NULL;
		if (isset($rule['default']))
		{
			$default = $rule['default'];
		}
		else
		{
			$defaultMethod = "getDefault$name";
			$defaultMethod{10} = $defaultMethod{10} & "\xDF"; // ucfirst
			if (method_exists($this, $defaultMethod))
			{
				$default = $this->{$defaultMethod}();
			}
		}
		return $default;
	}


	final private function setValueHelper($name, $value)
	{
		$rule = $this->rules[$name];

		if ($value === self::DEFAULT_VALUE)
		{
			$value = $this->getDefaultValueHelper($name, $rule);
		}

		if (($rule['relationship'] === MetaData::ManyToOne OR $rule['relationship'] === MetaData::OneToOne) AND !($value instanceof IEntity))
		{
			$id = (string) $value;
			if ($id)
			{
				$value = Model::get()->getRepository($rule['relationshipParam'])->getById($id);
			}
		}
		else if ($rule['relationship'] === MetaData::OneToMany OR $rule['relationship'] === MetaData::ManyToMany)
		{

			if (!isset($this->values[$name]) OR !($this->values[$name] instanceof IRelationship))
			{
				$tmp = new $rule['relationshipParam']($this);
			}
			else
			{
				$tmp = $this->values[$name];
			}
			if ($value !== NULL) $tmp->set($value);
			$value = $tmp;
		}

		if (isset($rule['enum']))
		{
			if (in_array($value, $rule['enum']['constants'], true)) {}
			else if (($tmp = array_search($value, $rule['enum']['constants'])) !== false)
			{
				$value = $rule['enum']['constants'][$tmp];
			}
			else if (in_array('null', $rule['types']) AND $value === NULL)
			{
				$value = NULL;
			}
			else
			{
				throw new UnexpectedValueException("Param ".get_class($this)."::\$$name must be '{$rule['enum']['original']}', '" . (is_object($value) ? 'object ' . get_class($value) : (is_scalar($value) ? $value : gettype($value))) . "' given");
			}
		}
		if (!ValidationHelper::isValid($rule['types'], $value))
		{
			$type = implode('|',$rule['types']);
			throw new UnexpectedValueException("Param ".get_class($this)."::\$$name must be '$type', '" . (is_object($value) ? get_class($value) : gettype($value)) . "' given");
		}

		$this->values[$name] = $value;
		$this->valid[$name] = true;
		$this->changed = true;
	}



	/**
	 * @internal
	 */
	final public static function ___create($entityName, array $data, IRepository $repository)
	{
		$entity = unserialize("O:".strlen($entityName).":\"$entityName\":0:{}");
		if (!($entity instanceof IEntity)) throw new InvalidStateException();
		// TODO kdyz je instanceof self tak pouzivat private pristup, jinak vymyslet neco jineho

		$entity->values = $data;
		$entity->valid = array();
		return $entity;
	}



}

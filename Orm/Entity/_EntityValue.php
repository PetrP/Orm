<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use InvalidArgumentException;
use Nette\MemberAccessException;
use Nette\UnexpectedValueException;
use Exception;
use ReflectionMethod;

/**
 * Obstarava cteni, kontrolu, plneni, nastavovani, validovani dat
 * @see Entity
 */
abstract class _EntityValue extends _EntityGeneratingRepository
{

	/** @var array of mixed Hodnoty parametru */
	private $values = array();

	/** @var array of bool Jsou parametry validni? */
	private $valid = array();

	/** @var array internal format MetaData */
	private $rules;

	/** @var bool Byla zmenena nejaka hodnota na teto entite od posledniho ulozeni? */
	private $changed = false;

	/** @var array Za behu informace ktere metody se volaji, aby bylo mozne magicke pretezovani */
	private $overwriteMethodTemp = array();

	/**
	 * Existuje tento parametr?
	 * Mozno i zjisti jestli je pro cteni/zapis.
	 * @param string
	 * @param int|NULL MetaData::READWRITE MetaData::READ MetaData::WRITE
	 * @return bool
	 */
	final public function hasParam($name, $mode = NULL)
	{
		if ($mode === NULL) return isset($this->rules[$name]);
		if (!isset($this->rules[$name])) return false;

		$rule = $this->rules[$name];
		if ($mode === MetaData::READWRITE) return isset($rule['get']) AND isset($rule['set']);
		else if ($mode === MetaData::READ) return isset($rule['get']);
		else if ($mode === MetaData::WRITE) return isset($rule['set']);
		throw new InvalidArgumentException('Unknown mode');
	}

	/**
	 * Pouziva se pouze ve vlastnich geterech.
	 * Vrati hodnotu parametru, ale nepouzije getter
	 * @param string
	 * @param bool
	 * @return mixed
	 */
	final protected function getValue($name, $need = true)
	{
		if (!isset($this->rules[$name]))
		{
			throw new MemberAccessException("Cannot read an undeclared property ".get_class($this)."::\$$name.");
		}
		unset($this->overwriteMethodTemp['get'][$name]);

		$rule = $this->rules[$name];

		if (!isset($rule['get']))
		{
			throw new MemberAccessException("Cannot read to a write-only property ".get_class($this)."::\$$name.");
		}

		$value = IEntity::DEFAULT_VALUE;
		$valid = false;
		if (isset($this->values[$name]) OR array_key_exists($name, $this->values))
		{
			$valid = isset($this->valid[$name]) ? $this->valid[$name] : false;
			$value = $this->values[$name];
		}
		else
		{
			$this->values[$name] = NULL; // zabranuje opakovanemu volani default a lazyload, kdyz setter nic nedela
			if ($this->getGeneratingRepository(false)) // lazy load
			{
				if ($lazyLoadParams = $this->getGeneratingRepository()->lazyLoad($this, $name))
				{
					foreach ($lazyLoadParams as $n => $v)
					{
						if ($name == $n OR !array_key_exists($n, $this->values))
						{
							$this->values[$n] = $v;
							$this->valid[$n] = false;
						}
					}
					if (array_key_exists($name, $this->values))
					{
						$value = $this->values[$name];
					}
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
					$this->valid[$name] = true; // zabranuje opakovanemu volani setteru, kdyz ten nic nedela
				}
				else
				{
					$this->setValueHelper($name, $value, $rule);
				}
			} catch (Exception $e) {
				$this->changed = $tmpChanged;
				if (!($e instanceof UnexpectedValueException) OR $need)
				{
					throw $e;
				}
				return NULL;
			}
			$this->changed = $tmpChanged;
			$value = isset($this->values[$name]) ? $this->values[$name] : NULL; // todo kdyz neni nastaveno muze to znamenat neco spatne, vyhodit chybu?
		}

		return $value;
	}

	/**
	 * Pouziva se pouze ve vlastnich seterech.
	 * Nastavi hodnotu parametru, ale nepouzije setter
	 * @param string
	 * @param mixed
	 * @return Entity $this
	 */
	final protected function setValue($name, $value)
	{
		if (!isset($this->rules[$name]))
		{
			throw new MemberAccessException("Cannot write to an undeclared property ".get_class($this)."::\$$name.");
		}
		unset($this->overwriteMethodTemp['set'][$name]);

		$rule = $this->rules[$name];

		if (!isset($rule['set']))
		{
			throw new MemberAccessException("Cannot write to a read-only property ".get_class($this)."::\$$name.");
		}

		$this->setValueHelper($name, $value, $rule);

		return $this;
	}

	/**
	 * Byla zmenena nejaka hodnota na teto entite od posledniho ulozeni?
	 * @param NULL|true
	 * @return bool
	 * @see self::$changed
	 */
	final public function isChanged($set = NULL)
	{
		if ($set === true)
		{
			$this->changed = true;
		}
		return $this->__isset('id') ? $this->changed : true;
	}

	/** Vytvorena nova entita */
	protected function onCreate()
	{
		parent::onCreate();
		$this->changed = true;
		$this->rules = MetaData::getEntityRules(get_class($this));
	}

	/**
	 * Pripojeno na repository
	 * @param IRepository
	 */
	protected function onAttach(IRepository $repository)
	{
		parent::onAttach($repository);
		$this->rules = MetaData::getEntityRules(get_class($this), $repository->getModel());
	}

	/**
	 * Vytazena z mapperu
	 * @param IRepository
	 * @param array
	 */
	protected function onLoad(IRepository $repository, array $data)
	{
		parent::onLoad($repository, $data);
		$this->rules = MetaData::getEntityRules(get_class($this), $repository->getModel());
		$this->values = $data;
		$this->valid = array();
		$this->getValue('id'); // throw error if any
	}

	/**
	 * Behem persistovani, vsechny subentity nemusi byt jeste persistovany
	 * @param IRepository
	 * @param scalar
	 */
	final protected function onPersist(IRepository $repository, $id)
	{
		parent::onPersist($repository, $id);
		$this->values['id'] = $id;
		$this->valid['id'] = false;
		$this->getValue('id'); // throw error if any
		$this->changed = false;
	}

	/**
	 * Po vymazani
	 * @param IRepository
	 */
	protected function onAfterRemove(IRepository $repository)
	{
		parent::onAfterRemove($repository);
		$this->values['id'] = NULL;
		$this->valid['id'] = false;
		$this->changed = true;
	}

	/** Pri klonovani vznika nova entita se stejnejma datama */
	public function __clone()
	{
		$this->values['id'] = NULL;
		$this->valid['id'] = false;
		$this->changed = true;
	}

	/**
	 * Pristup k parametru jako k property.
	 * @param string
	 * @return mixed
	 * @throws MemberAccessException
	 */
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
			if (!isset($this->overwriteMethodTemp['get'][$name]))
			{
				$this->overwriteMethodTemp['get'][$name] = true;
				try {
					$value = $this->{$rule['get']['method']}(); // todo mohlo by zavolat private metodu, je potreba aby vse bylo final
				} catch (Exception $e) {
					unset($this->overwriteMethodTemp['get'][$name]);
					throw $e;
				}
				unset($this->overwriteMethodTemp['get'][$name]);
			}
			else
			{
				unset($this->overwriteMethodTemp['get'][$name]);
				$value = $this->getValue($name);
			}
		}
		else
		{
			$value = $this->getValue($name);
		}

		return $value;
	}

	/**
	 * Nastav parametr jako property.
	 * @param string
	 * @param mixed
	 * @return Entity $this
	 * @throws MemberAccessException
	 * @throws UnexpectedValueException
	 */
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
			if ($value === IEntity::DEFAULT_VALUE)
			{
				$value = $this->getDefaultValueHelper($name, $rule);
			}
			if (!isset($this->overwriteMethodTemp['set'][$name]))
			{
				$this->overwriteMethodTemp['set'][$name] = true;
				try {
					$this->{$rule['set']['method']}($value); // todo mohlo by zavolat private metodu, je potreba aby vse bylo final
				} catch (Exception $e) {
					unset($this->overwriteMethodTemp['set'][$name]);
					throw $e;
				}
				unset($this->overwriteMethodTemp['set'][$name]);
			}
			else
			{
				unset($this->overwriteMethodTemp['set'][$name]);
				return $this->setValue($name, $value);
			}
		}
		else
		{
			$this->setValue($name, $value);
		}

		return $this;
	}

	/**
	 * Pristup/nastaveni parametru prese getter/setter `get<Param>` `set<Param>`
	 * @param string
	 * @param array
	 * @return mixed
	 * @throws MemberAccessException
	 * @throws UnexpectedValueException
	 */
	final public function __call($name, $args)
	{
		$m = $var = NULL;
		if (strncmp($name, 'get', 3) === 0 OR (strncmp($name, 'set', 3) === 0 AND array_key_exists(0, $args)))
		{
			$m = substr($name, 0, 3);
			$var = substr($name, 3);
		}
		else if (strncmp($name, 'is', 2) === 0)
		{
			$m = 'is';
			$var = substr($name, 2);
		}

		if ($m !== NULL)
		{
			if (PHP_VERSION_ID < 50300 AND !preg_match('#[A-Z]#', $var))
			{
				// php 5.2 spatne predava name pri magickem pretezovani, name je cely lowercase
				// @codeCoverageIgnoreStart
				foreach ($this->rules as $key => $foo)
				{
					if (strcasecmp($key, $var) === 0)
					{
						$var = $key;
						break;
					}
				}
			}	// @codeCoverageIgnoreEnd
			if ($var{0} != '_') $var{0} = $var{0} | "\x20"; // lcfirst
			if (isset($this->rules[$var]))
			{
				if ($m !== 'is' OR ($m === 'is' AND $this->rules[$var]['types'] === array('bool' => 'bool')))
				{
					if ($m === 'is') $m = 'get';
					$this->overwriteMethodTemp[$m][$var] = true;
					return $this->{'__' . $m}($var, $m === 'set' ? $args[0] : NULL);
				}
			}
		}

		return parent::__call($name, $args);
	}

	/**
	 * Existuje parametr?
	 * @param string
	 * @return bool
	 */
	final public function __isset($name)
	{
		if (isset($this->rules[$name]['get']))
		{
			if (isset($this->valid[$name]) AND $this->valid[$name])
			{
				return isset($this->values[$name]);
			}
			try {
				return $this->__get($name) !== NULL;
			} catch (Exception $e) {
				return false;
			}
		}

		return parent::__isset($name);
	}

	/**
	 * Vrati defaultni hodnotu parametru.
	 * @param string
	 * @param array
	 * @return mixed
	 */
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
				$r = new ReflectionMethod($this, $defaultMethod);
				// todo predelat aby se methody zjistovali jednou v MetaData
				if (!$r->isPrivate())
				{
					$default = $this->{$defaultMethod}();
				}
			}
		}
		return $default;
	}

	/**
	 * Nastavi parametr na hodnotu.
	 * @param string
	 * @param mixed
	 * @return void
	 * @throws UnexpectedValueException
	 */
	final private function setValueHelper($name, $value, $rule)
	{
		if ($value === IEntity::DEFAULT_VALUE)
		{
			$value = $this->getDefaultValueHelper($name, $rule);
		}

		if ($rule['relationship'] === MetaData::ManyToOne OR $rule['relationship'] === MetaData::OneToOne)
		{
			if (!($value instanceof IEntity) AND $value !== NULL)
			{
				$id = (string) $value;
				$repo = $this->getModel()->getRepository($rule['relationshipParam']);
				$value = $repo->getById($id);
				if (!$value AND !isset($rule['types']['null']))
				{
					$type = implode('|',$rule['types']);
					throw new UnexpectedValueException("Entity($type) '$id' not found in `" . get_class($repo) . "` in ".get_class($this)."::\$$name");
				}
			}
		}
		else if (isset($rule['injection']))
		{
			if (!isset($this->values[$name]) OR !($this->values[$name] instanceof IEntityInjection))
			{
				$xValues = isset($this->values[$name]) ? $this->values[$name] : NULL;
				$tmp = $rule['injection']->invoke($this, $xValues);
				if (!($tmp instanceof IEntityInjection))
				{
					throw new UnexpectedValueException("Param ".get_class($this)."::\$$name must be 'Orm\\IEntityInjection', '" . (is_object($tmp) ? 'object ' . get_class($tmp) : (is_scalar($tmp) ? $tmp : gettype($tmp))) . "' given");
				}
				if ($xValues !== $value) $tmp->setInjectedValue($value);
			}
			else
			{
				$tmp = $this->values[$name];
				$tmp->setInjectedValue($value);
			}
			$value = $tmp;
		}

		if (isset($rule['enum']))
		{
			if (in_array($value, $rule['enum']['constants'], true)) {}
			else if (($tmp = array_search($value, $rule['enum']['constants'])) !== false)
			{
				$value = $rule['enum']['constants'][$tmp];
			}
			else if (isset($rule['types']['null']) AND $value === NULL)
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
	 * Call to undefined static method.
	 * @param string
	 * @param array
	 * @return mixed
	 * @throws MemberAccessException
	 */
	public static function __callStatic($name, $args)
	{
		// @codeCoverageIgnoreStart
		if (PHP_VERSION_ID === 50303)
		{
			// fix for bug 5.3.3 #52713; Generates a warning, because too slow.
			if (strncmp($name, 'get', 3) === 0 OR strncmp($name, 'is', 3) === 0 OR strncmp($name, 'set', 3) === 0)
			{
				$m = substr($name, 0, 1);
				$var = substr($name, 3);
				if ($m === 'i')
				{
					$m = 'g';
					$var = substr($name, 2);
				}
				if ($var{0} != '_') $var{0} = $var{0} | "\x20"; // lcfirst
				$message = "setValue('$var', \$value) instead of parent::$name(\$value)";
				if ($m === 'g')
				{
					$message = "getValue('$var') instead of parent::$name()";
				}
				trigger_error("php 5.3.3 bug #52713; Upgrade php or use \$this->$message", E_USER_WARNING);
				foreach (debug_backtrace() as $trace)
				{
					if (isset($trace['object']) AND $trace['object'] instanceof IEntity)
					{
						return $trace['object']->__call($name, $args);
					}
				}
			}
		}
		return parent::__callStatic($name, $args);
	}	// @codeCoverageIgnoreEnd

}

<?php

require_once dirname(__FILE__) . '/Entity/Entity.php';

require_once dirname(__FILE__) . '/Repository/Repository.php';

require_once dirname(__FILE__) . '/Mappers/Mapper.php';

abstract class AbstractModel extends Object
{
	static private $instance; // todo di

	private $repositories = array();

	public function __construct()
	{
		if (!($this instanceof Model))
		{
			throw new InvalidStateException();
		}
		if (!isset(self::$instance))
		{
			self::$instance = $this;
		}
	}

	public static function get() // todo di
	{
		if (!isset(self::$instance))
		{
			throw new InvalidStateException();
		}
		return self::$instance;
	}

	/**
	 * @return Repository
	 */
	public function getRepository($name)
	{
		$name = strtolower($name);
		if (!isset($this->repositories[$name]))
		{
			$class = $this->getRepositoryClass($name);
			$this->checkRepositoryClass($class, $name);
			$this->repositories[$name] = new $class($name, $this);
		}
		return $this->repositories[$name];
	}

	/**
	 * @param string
	 * @return bool
	 */
	final public function isRepository($name)
	{
		$name = strtolower($name);
		if (isset($this->repositories[$name])) return true;
		try {
			return $this->checkRepositoryClass($this->getRepositoryClass($name), $name);
		} catch (InvalidStateException $e) {
			return false;
		}
	}

	/**
	 * @param string
	 * @param string
	 * @return bool
	 * @throws InvalidStateException
	 */
	final private function checkRepositoryClass($class, $name)
	{
		if (!class_exists($class))
		{
			throw new InvalidStateException("Repository '{$name}' doesn't exists");
		}

		$reflection = new ClassReflection($class);

		if (!$reflection->implementsInterface('IRepository'))
		{
			throw new InvalidStateException("Repository '{$name}' must implement IRepository");
		}
		else if ($reflection->isAbstract())
		{
			throw new InvalidStateException("Repository '{$name}' is abstract.");
		}
		else if (!$reflection->isInstantiable())
		{
			throw new InvalidStateException("Repository '{$name}' isn't instantiable");
		}

		return true;
	}

	/**
	 * @throws UnexpectedValueException
	 * @param string
	 * @return string
	 */
	final private function getRepositoryClass($name)
	{
		$class = $name . 'Repository';
		$class[0] = strtoupper($class[0]);
		return $class;
	}

	public function & __get($name)
	{
		$r = $this->getRepository($name);
		return $r;
	}

	final public function flush()
	{
		foreach ($this->repositories as $repo)
		{
			$repo->flush(true);
		}
	}

	final public function clean()
	{
		foreach ($this->repositories as $repo)
		{
			$repo->clean(true);
		}
	}

}

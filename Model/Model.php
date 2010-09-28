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
			$r = new $class($name, $this);
			if (!($r instanceof IRepository))
			{
				throw new InvalidStateException("Repository '{$r}' must implement IRepository");
			}
			$this->repositories[$name] = $r;
		}
		return $this->repositories[$name];
	}

	final public function isRepository($name)
	{
		$name = strtolower($name);
		if (isset($this->repositories[$name])) return true;
		try {
			$implements = class_implements($this->getRepositoryClass($name));
			return isset($implements['IRepository']);
		} catch (UnexpectedValueException $e) {
			return false;
		}
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

		if (!class_exists($class))
		{
			throw new UnexpectedValueException("Repository '{$name}' doesn't exists");
		}
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

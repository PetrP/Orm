<?php

require_once dirname(__FILE__) . '/Entity/Entity.php';

require_once dirname(__FILE__) . '/Repository/Repository.php';

require_once dirname(__FILE__) . '/Mappers/Mapper.php';


abstract class AbstractModel extends Object
{
	private static $repositories = array();
	private static $model;

	/**
	 * @return Repository
	 */
	public static function getRepository($name)
	{
		$name = strtolower($name);
		if (!isset(self::$repositories[$name]))
		{
			$class = self::getRepositoryClass($name);
			$r = new $class($name);
			if (!($r instanceof IRepository))
			{
				throw new InvalidStateException();
			}
			self::$repositories[$name] = $r;
		}
		return self::$repositories[$name];
	}

	public static function isRepository($name)
	{
		$name = strtolower($name);
		if (isset(self::$repositories[$name])) return true;
		try {
			$implements = class_implements(self::getRepositoryClass($name));
			return isset($implements['IRepository']);
		} catch (InvalidStateException $e) {
			return false;
		}
	}

	/**
	 * @throws InvalidStateException
	 * @param string
	 * @return string
	 */
	private static function getRepositoryClass($name)
	{
		$class = $name . 'Repository';
		$class[0] = strtoupper($class[0]);

		if (!class_exists($class))
		{
			throw new InvalidStateException();
		}
		return $class;
	}

	public function & __get($name)
	{
		$r = $this->getRepository($name);
		return $r;
	}

	/**
	 * @return Model
	 */
	public static function get()
	{
		if (!isset(self::$model))
		{
			$model = new Model;
			if (!($model instanceof self))
			{
				throw new Exception;
			}
			self::$model = $model;
		}
		return self::$model;
	}

	final public static function flush()
	{
		foreach (self::$repositories as $repo)
		{
			$repo->flush(true);
		}
	}

	final public static function clean()
	{
		foreach (self::$repositories as $repo)
		{
			$repo->clean(true);
		}
	}

}

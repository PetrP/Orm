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
			$class = $name . 'Repository';
			$class[0] = strtoupper($class[0]);

			$r = new $class($name);
			if (!($r instanceof Repository))
			{
				throw new InvalidStateException();
			}
			self::$repositories[$name] = $r;
		}
		return self::$repositories[$name];
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

}







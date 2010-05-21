<?php


abstract class AbstractModel extends Object
{
	private static $repositories = array();

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
	 * @return AppModel
	 */
	public static function get()
	{
		static $model;
		if (!isset($model))
		{
			$model = new Model;
			if (!($model instanceof self))
			{
				throw new Error;
			}
		}
		return $model;
	}

}







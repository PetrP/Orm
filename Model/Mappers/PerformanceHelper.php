<?php

class PerformanceHelper extends Object
{
	public static $keyCallback = array(__CLASS__, 'getDefaultKey');

	private $repositoryName;

	private $access = array();

	public static $toSave;
	private static $toLoad;

	private $key;

	public function __construct(IRepository $repository)
	{
		$this->repositoryName = $repository->getRepositoryName();
		if (!isset(self::$toLoad))
		{
			$cache = Environment::getCache(__CLASS__);
			$key = self::$keyCallback ? (string) callback(self::$keyCallback)->invoke() : NULL;
			$key = $key ? $key : '*';
			self::$toLoad = $cache[$key];
			if ($key === '*')
			{
				self::$toSave = self::$toLoad;
			}
			register_shutdown_function(create_function('$cache, $key', '
				$cache[$key] = PerformanceHelper::$toSave;
			'), $cache, $key);
		}

		if (!isset(self::$toSave[$this->repositoryName])) self::$toSave[$this->repositoryName] = array();
		$this->access = & self::$toSave[$this->repositoryName];
	}

	public function access($id)
	{
		$this->access[$id] = $id;
	}

	public function get()
	{
		$tmp = isset(self::$toLoad[$this->repositoryName]) ? self::$toLoad[$this->repositoryName] : NULL;
		self::$toLoad[$this->repositoryName] = NULL;
		return $tmp;
	}

	private function getCache()
	{
		return Environment::getCache(__CLASS__);
	}

	public static function getDefaultKey()
	{
		return isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : NULL;
	}
}

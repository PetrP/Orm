<?php

class PerformanceHelper extends Object
{
	private $repositoryName;

	private $access = array();

	public static $toSave;
	private static $toLoad;

	private $key;

	public function __construct(Repository $repository)
	{
		$this->repositoryName = $repository->getRepositoryName();
		if (!isset(self::$toLoad))
		{
			$cache = Environment::getCache(__CLASS__);
			$key = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '*';
			self::$toLoad = $cache[$key];
			register_shutdown_function(create_function('$cache, $key', '
				$cache[$key] = PerformanceHelper::$toSave;
			'), $cache, $key);
		}

		self::$toSave[$this->repositoryName] = array();
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

}

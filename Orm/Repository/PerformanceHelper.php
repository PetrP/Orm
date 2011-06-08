<?php

namespace Orm;

use Nette\Object;
use Nette\Environment;

/**
 * @todo refactoring
 */
class PerformanceHelper extends Object
{
	public static $keyCallback = array(__CLASS__, 'getDefaultKey');

	private $repositoryName;

	private $access = array();

	public static $toSave;
	private static $toLoad;

	public function __construct(IRepository $repository)
	{
		$this->repositoryName = $repository->getRepositoryName();
		if (!isset(self::$toLoad))
		{
			$cache = $this->getCache();
			$key = self::$keyCallback ? (string) callback(self::$keyCallback)->invoke() : NULL;
			$key = $key ? $key : '*';
			self::$toLoad = isset($cache[$key]) ? $cache[$key] : NULL;
			if (!self::$toLoad) self::$toLoad = array();
			if ($key === '*')
			{
				self::$toSave = self::$toLoad;
			}
			else if (strlen($key) > 50)
			{
				$key = substr($key, 0, 20) . md5($key);
			}

			register_shutdown_function(function ($cache, $key) {
				$cache[$key] = \Orm\PerformanceHelper::$toSave;
			}, $cache, $key);
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

	protected function getCache()
	{
		return Environment::getCache(__CLASS__);
	}

	public static function getDefaultKey()
	{
		return isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : NULL;
	}
}

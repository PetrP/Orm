<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use ArrayAccess;

/**
 * Remembers used ids and then allows load all in one query.
 * @see Repository::getById
 * @author Petr Procházka
 * @package Orm
 * @subpackage Repository\Helpers
 * @todo refactoring
 * @todo neumoznuje nekolik stranek najednou (premazava se)
 */
class PerformanceHelper extends Object
{

	/**
	 * Callback pro klic pod kterym se uklada, defaultne je to url stranky.
	 * @var callback
	 */
	public static $keyCallback = array(__CLASS__, 'getDefaultKey');

	/** @var string */
	private $repositoryClass;

	/** @var array reference na self::$toSave */
	private $access = array();

	/** @var array @internal */
	public static $toSave;

	/** @var array */
	private static $toLoad;

	/**
	 * @param IRepository
	 * @param ArrayAccess
	 */
	public function __construct(IRepository $repository, ArrayAccess $cache)
	{
		if (!static::$keyCallback) return;
		$this->repositoryClass = get_class($repository);
		if (self::$toLoad === NULL)
		{
			$key = static::$keyCallback ? (string) Callback::create(static::$keyCallback)->invoke() : NULL;
			$key = $key ? $key : '*';
			if (strlen($key) > 50)
			{
				$key = substr($key, 0, 20) . md5($key);
			}
			self::$toLoad = isset($cache[$key]) ? $cache[$key] : NULL;
			if (!self::$toLoad) self::$toLoad = array();
			if ($key === '*')
			{
				self::$toSave = self::$toLoad;
			}

			register_shutdown_function(function ($cache, $key) {
				// @codeCoverageIgnoreStart
				$cache[$key] = \Orm\PerformanceHelper::$toSave;
				// @codeCoverageIgnoreEnd
			}, $cache, $key);
		}

		if (!isset(self::$toSave[$this->repositoryClass])) self::$toSave[$this->repositoryClass] = array();
		$this->access = & self::$toSave[$this->repositoryClass];
	}

	/**
	 * Rika ze bylo potreba toto id
	 * @param scalar
	 */
	public function access($id)
	{
		$this->access[$id] = $id;
	}

	/**
	 * Vrati vsechny id ktere asi budou potreba a vyprazdni je.
	 * Lze zavolat jen jednou.
	 * @return array of id
	 */
	public function get()
	{
		$tmp = NULL;
		if (isset(self::$toLoad[$this->repositoryClass]))
		{
			$tmp = self::$toLoad[$this->repositoryClass];
			self::$toLoad[$this->repositoryClass] = NULL;
		}
		return $tmp;
	}

	/** @deprecated */
	final protected function getCache()
	{
		throw new DeprecatedException(array(__CLASS__, 'getCache()', 'constructor injection'));
	}

	/**
	 * @see self::$keyCallback
	 * @return string|NULL
	 */
	public static function getDefaultKey()
	{
		return isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : NULL;
	}
}

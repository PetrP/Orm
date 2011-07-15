<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use Nette\Object;
use Nette\Environment;

/**
 * Pamatuje si ktere id byli na teto strance potreba a umoznuje je pak nacist najednou jednim dotazem.
 * @see Repository::getById
 * @todo refactoring
 * @todo umoznit vypnout
 * @todo neumoznuje nekolik stranek najednou (premazava se)
 * @todo Environment
 */
class PerformanceHelper extends Object
{

	/**
	 * Callback pro klic pod kterym se uklada, defaultne je to url stranky.
	 * @var callback
	 */
	public static $keyCallback = array(__CLASS__, 'getDefaultKey');

	/** @var string */
	private $repositoryName;

	/** @var array reference na self::$toSave */
	private $access = array();

	/** @var array @internal */
	public static $toSave;

	/** @var array */
	private static $toLoad;

	/** @param IRepository */
	public function __construct(IRepository $repository)
	{
		if (!self::$keyCallback) return;
		$this->repositoryName = $repository->getRepositoryName();
		if (!isset(self::$toLoad))
		{
			$cache = $this->getCache();
			$key = self::$keyCallback ? (string) callback(self::$keyCallback)->invoke() : NULL;
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

		if (!isset(self::$toSave[$this->repositoryName])) self::$toSave[$this->repositoryName] = array();
		$this->access = & self::$toSave[$this->repositoryName];
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
		if (isset(self::$toLoad[$this->repositoryName]))
		{
			$tmp = self::$toLoad[$this->repositoryName];
			self::$toLoad[$this->repositoryName] = NULL;
		}
		return $tmp;
	}

	/** @return Nette\Caching\Cache */
	protected function getCache()
	{
		return Environment::getCache(__CLASS__);
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

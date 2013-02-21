<?php
/**
 * Orm
 * @author Petr Procházka (petr@petrp.cz)
 * @license "New" BSD License
 */

namespace Orm;

use RuntimeException;

/**
 * @see MetaData::getEntityRules()
 * @author Petr Procházka
 * @package Orm
 * @subpackage Entity\MetaData
 */
class MetaDataNestingLevelException extends RuntimeException
{

	/** @var int */
	const MAX_DEEP = 11;

	/** @var int */
	private static $deep = 0;

	/** @var MetaDataNestingLevelException */
	private static $traceLessInstance;

	/**
	 * Get instance of this exception with no stack trace.
	 * @return MetaDataNestingLevelException
	 */
	public static function getTraceLessInstance()
	{
		if (self::$traceLessInstance === NULL)
		{
			self::$traceLessInstance = unserialize('O' . substr(serialize(__CLASS__), 1, -1) . ":3:{s:16:\"\x00Exception\x00trace\";a:0:{}s:7:\"\x00*\x00file\";s:0:\"\";s:7:\"\x00*\x00line\";i:0;}");
		}
		return self::$traceLessInstance;
	}

	/** @throws MetaDataNestingLevelException */
	public static function check()
	{
		if (self::$deep > self::MAX_DEEP)
		{
			throw self::getTraceLessInstance();
		}
	}

	/** @return bool */
	public static function isMaxDeep()
	{
		return self::$deep === self::MAX_DEEP;
	}

	public static function start()
	{
		self::$deep++;
	}

	public static function stop()
	{
		self::$deep--;
	}

}

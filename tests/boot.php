<?php

require_once __DIR__ . '/libs/Nette/loader.php';
require_once __DIR__ . '/libs/dump.php';
require_once __DIR__ . '/libs/dibi/dibi.php';
require_once __DIR__ . '/../Orm/Orm.php';

use Nette\Diagnostics\Debugger as Debug;
use Nette\Environment;
use Nette\Loaders\RobotLoader;
use Nette\InvalidStateException;

Debug::enable(false);
Debug::$strictMode = true;

date_default_timezone_set('Europe/Prague');

Environment::setVariable('tempDir', __DIR__ . '/tmp');

try {
	$storage = Environment::getService(str_replace('-', '\\', 'Nette-Caching-ICacheStorage'));
} catch (InvalidStateException $e) {
	$storage = Environment::getContext()->cacheStorage;
}

$r = new RobotLoader;
$r->setCacheStorage($storage);
$r->addDirectory(__DIR__ . '/libs');
$r->addDirectory(__DIR__ . '/unit');
$r->register();

require_once __DIR__ . '/../Orm/Mappers/Collection/DataSourceCollection.php';
require_once __DIR__ . '/unit/Mappers/DibiMockEscapeMySqlDriver.php';
require_once __DIR__ . '/unit/Mappers/DibiMockExpectedMySqlDriver.php';

abstract class TestCase extends PHPUnit_Framework_TestCase
{

	public static function readAttribute($classOrObject, $attributeName)
	{
		try {
			return parent::readAttribute($classOrObject, $attributeName);
		} catch (PHPUnit_Framework_ExpectationFailedException $e) {
			if (is_object($classOrObject) AND $e->getMessage() == 'Failed asserting that object of class "'.get_class($classOrObject).'" has attribute "'.$attributeName.'".')
			{
				$needle = "\0$attributeName";
				foreach ((array) $classOrObject as $key => $value)
				{
					if (substr($key, -strlen($needle)) === $needle)
					{
						return $value;
					}
				}
			}
			throw $e;
		}
	}

	public static function assertAttributeSame($expected, $actualAttributeName, $actualClassOrObject, $message = '')
	{
		self::assertSame(
			$expected,
			self::readAttribute($actualClassOrObject, $actualAttributeName),
			$message
		);
	}

}

use Orm\PerformanceHelper;

PerformanceHelper::$keyCallback = create_function('', 'return md5(lcg_value()) . md5(lcg_value()) . md5(lcg_value());');

function setAccessible(ReflectionProperty $r)
{
	if (!$r->isPrivate())
	{
		throw new Exception();
	}
	if (PHP_VERSION_ID < 50300)
	{
		throw new PHPUnit_Framework_IncompleteTestError('php 5.2 (setAccessible)');
	}
	$r->setAccessible(true);
}

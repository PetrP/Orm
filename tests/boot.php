<?php

require_once __DIR__ . '/libs/Nette/loader.php';
require_once __DIR__ . '/libs/dump.php';
require_once __DIR__ . '/libs/dibi/dibi.php';
require_once __DIR__ . '/libs/HttpPHPUnit/ResultPrinter/NetteDebug.php';
if (!class_exists('Orm\Orm')) require_once __DIR__ . '/../Orm/Orm.php';

use Nette\Environment;
use Nette\Loaders\RobotLoader;
use HttpPHPUnit\NetteDebug;

NetteDebug::get()->enable(false);
NetteDebug::get()->strictMode = true;

date_default_timezone_set('Europe/Prague');

Environment::setVariable('tempDir', __DIR__ . '/tmp');

try {
	$storage = Environment::getService(str_replace('-', '\\', 'Nette-Caching-ICacheStorage'));
} catch (Exception $e) {
	unset($e);
	$storage = Environment::getContext()->cacheStorage;
}

$r = new RobotLoader;
$r->setCacheStorage($storage);
$r->addDirectory(__DIR__ . '/libs');
$r->addDirectory(__DIR__ . '/cases');
$r->register();
unset($r, $storage);

require_once __DIR__ . '/cases/Mappers/DibiMockEscapeMySqlDriver.php';
require_once __DIR__ . '/cases/Mappers/DibiMockExpectedMySqlDriver.php';

use Orm\PerformanceHelper;

PerformanceHelper::$keyCallback = NULL;

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
	return $r;
}

<?php

require_once __DIR__ . '/loader.php';
unset($robotLoader, $storage);
if (!class_exists('Orm\Orm')) require_once __DIR__ . '/../Orm/Orm.php';
require_once __DIR__ . '/cases/Mappers/DibiMockEscapeMySqlDriver.php';
require_once __DIR__ . '/cases/Mappers/DibiMockExpectedMySqlDriver.php';

if (isset($_SERVER['ORIG_SCRIPT_FILENAME']) AND PHPUnit_Util_PHP::getPhpBinary() === 'php')
{
	PHPUnit_Util_PHP::setPhpBinary($_SERVER['ORIG_SCRIPT_FILENAME']);
}

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

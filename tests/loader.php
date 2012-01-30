<?php

require_once __DIR__ . '/libs/Nette/loader.php';
require_once __DIR__ . '/libs/dump.php';
require_once __DIR__ . '/libs/dibi/dibi.php';
require_once __DIR__ . '/libs/HttpPHPUnit/ResultPrinter/NetteDebug.php';

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

$robotLoader = new RobotLoader;
$robotLoader->setCacheStorage($storage);
$robotLoader->addDirectory(__DIR__ . '/libs');
$robotLoader->addDirectory(__DIR__ . '/cases');
$robotLoader->register();

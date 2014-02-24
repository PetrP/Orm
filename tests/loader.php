<?php

require_once __DIR__ . '/libs/Nette/loader.php';
require_once __DIR__ . '/libs/dump.php';
require_once __DIR__ . '/libs/dibi/dibi.php';
require_once __DIR__ . '/libs/HttpPHPUnit/ResultPrinter/NetteDebug.php';
require_once __DIR__ . '/libs/Access/Init.php';

use Nette\Environment;
use Nette\Loaders\RobotLoader;
use HttpPHPUnit\NetteDebug;
use Nette\Config\Configurator;

if (!isset($isInSeparateProcess))
{
	NetteDebug::get()->enable(false);
	NetteDebug::get()->strictMode = true;
}

date_default_timezone_set('Europe/Prague');

$configurator = new Configurator;
$tmpDir = __DIR__ . '/tmp';
@mkdir($tmpDir, 0777);
$configurator->setTempDirectory($tmpDir);

$robotLoader = $configurator->createRobotLoader();
$robotLoader->addDirectory(__DIR__ . '/libs');
$robotLoader->addDirectory(__DIR__ . '/cases');
$robotLoader->register();

$configurator->createContainer();

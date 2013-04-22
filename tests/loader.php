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

NetteDebug::get()->enable(false);
NetteDebug::get()->strictMode = true;

date_default_timezone_set('Europe/Prague');

$configurator = new Configurator;
$configurator->setTempDirectory(__DIR__ . '/tmp');

$robotLoader = $configurator->createRobotLoader();
$robotLoader->addDirectory(__DIR__ . '/libs');
$robotLoader->addDirectory(__DIR__ . '/cases');
$robotLoader->register();

$configurator->createContainer();

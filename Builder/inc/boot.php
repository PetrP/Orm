<?php

require_once __DIR__ . '/../libs/nette.min.php';
require_once __DIR__ . '/../../tests/libs/dump.php';

use Nette\Diagnostics\Debugger;
use Nette\Loaders\RobotLoader;
use Nette\Caching\Storages\FileStorage;

Debugger::enable();
Debugger::$strictMode = true;
set_time_limit(0);

$r = new RobotLoader;
$r->setCacheStorage(new FileStorage(__DIR__ . '/../../tests/tmp'));
$r->addDirectory(__DIR__ . '/../inc');
$r->addDirectory(__DIR__ . '/../libs');
$r->register();

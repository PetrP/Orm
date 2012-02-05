<?php

require_once __DIR__ . '/libs/Nette/loader.php';
require_once __DIR__ . '/libs/HttpPHPUnit/init.php';
$ormDir = realpath(__DIR__ . '/../../Orm');
require_once $ormDir . '\Orm.php';

callback(function () {

	$http = new HttpPHPUnit;

	require_once __DIR__ . '/boot.php';

	$http->run(__DIR__ . '/cases');

})->invoke();

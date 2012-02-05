<?php

require_once dirname(__FILE__) . '/libs/Nette/loader.php';
require_once dirname(__FILE__) . '/libs/HttpPHPUnit/init.php';
$ormDir = realpath(dirname(__FILE__) . '/../Orm');
require_once $ormDir . '\Orm.php';

callback(function () {

	$http = new HttpPHPUnit;

	require_once __DIR__ . '/boot.php';

	$http->run(__DIR__ . '/cases');

})->invoke();

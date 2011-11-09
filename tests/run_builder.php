<?php

require_once __DIR__ . '/../Builder/inc/boot.php';
require_once __DIR__ . '/libs/HttpPHPUnit/init.php';

callback(function (Nette\Loaders\RobotLoader $r) {

	$http = new HttpPHPUnit;

	$r->addDirectory(__DIR__ . '/cases_builder');
	$r->register();

	$http->run(__DIR__ . '/cases_builder');

})->invoke($r);

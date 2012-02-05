<?php

require_once dirname(__FILE__) . '/libs/Nette/loader.php';
require_once dirname(__FILE__) . '/libs/HttpPHPUnit/init.php';
$ormDir = realpath(dirname(__FILE__) . '/../../Orm');
require_once $ormDir . '\Orm.php';

function __run() {

	$http = new HttpPHPUnit;

	require_once dirname(__FILE__) . '/boot.php';

	$http->run(dirname(__FILE__) . '/cases');

}
callback('__run')->invoke();

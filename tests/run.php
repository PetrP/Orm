<?php

require_once dirname(__FILE__) . '/libs/Nette/loader.php';
require_once dirname(__FILE__) . '/libs/HttpPHPUnit/init.php';

$http = new HttpPHPUnit;
$http->coverage(dirname(__FILE__) . '/../Orm', dirname(__FILE__) . '/report');
$http->run(dirname(__FILE__) . '/unit');

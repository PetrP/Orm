<?php

require_once dirname(__FILE__) . '/libs/Nette/loader.php';
require_once dirname(__FILE__) . '/libs/HttpPHPUnit/init.php';

$http = new HttpPHPUnit;

require_once __DIR__ . '/boot.php';

$http->structure();

$c = $http->coverage(dirname(__FILE__) . '/../Orm', dirname(__FILE__) . '/report');
$c->filter()->removeFileFromWhitelist(dirname(__FILE__) . '/../Orm/Relationships/bc1m.php');
$c->filter()->removeFileFromWhitelist(dirname(__FILE__) . '/../Orm/Relationships/bcmm.php');

$http->run(dirname(__FILE__) . '/unit');

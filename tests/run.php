<?php

require_once __DIR__ . '/libs/Nette/loader.php';
require_once __DIR__ . '/libs/HttpPHPUnit/init.php';

$http = new HttpPHPUnit;

require_once __DIR__ . '/boot.php';

$http->structure();

$c = $http->coverage(__DIR__ . '/../Orm', __DIR__ . '/report');
$c->filter()->removeFileFromWhitelist(__DIR__ . '/../Orm/Relationships/bc1m.php');
$c->filter()->removeFileFromWhitelist(__DIR__ . '/../Orm/Relationships/bcmm.php');

$http->run(__DIR__ . '/unit');

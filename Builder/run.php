<?php

namespace Orm\Builder;

require_once __DIR__ . '/inc/boot.php';

$mode = isset($_GET['mode']) ? $_GET['mode'] : NULL;
$version = isset($_GET['version']) ? $_GET['version'] : NULL;

$git = new Git(__DIR__ . '/..');
$versions = new VersionsService($git);

if (!$mode)
{
	include __DIR__ . '/inc/Form.html.php';
}
else
{
	$runner = new Runner($git);
	$info = $runner->run($mode, $version);
	include __DIR__ . '/inc/Ready.html.php';
}
exit;

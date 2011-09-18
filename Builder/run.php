<?php

namespace Orm\Builder;

require_once __DIR__ . '/libs/nette.min.php';
require_once __DIR__ . '/../tests/libs/dump.php';

use Nette\Diagnostics\Debugger;
use Nette\Loaders\RobotLoader;
use Nette\Caching\Storages\FileStorage;

Debugger::enable();
Debugger::$strictMode = true;
set_time_limit(0);

$r = new RobotLoader;
$r->setCacheStorage(new FileStorage(__DIR__ . '/../tests/tmp'));
$r->addDirectory(__DIR__ . '/inc');
$r->addDirectory(__DIR__ . '/libs');
$r->register();

$isDev = isset($_GET['dev']);

$zip = new Zipper(__DIR__ . '/Orm.zip', __DIR__);
if (!$isDev)
{
	$zip->addMatch(__DIR__ . '/../Orm');
}

foreach (array(
	Builder::NS | Builder::NS_NETTE => 'php53/Nette_with_namespaces',
	Builder::NS | Builder::NONNS_NETTE => 'php53/Nette_without_namespaces',
	//Builder::NS | Builder::PREFIXED_NETTE => 'php53/Nette_prefixed',
	Builder::NONNS | Builder::NONNS_NETTE => 'php52/Nette_without_namespaces',
	Builder::NONNS | Builder::NS_NETTE => 'php52/Nette_with_namespaces',
	//Builder::NS | Builder::PREFIXED_NETTE => 'php52/Nette_prefixed',
) as $version => $dir)
{
	$b = new Builder($version, $isDev);
	foreach (array(
		'Orm',
		'tests/cases',
		'tests/boot.php',
		'tests/libs/HttpPHPUnit',
		'tests/libs/dump.php',
	) as $p)
	{
		$b->build(__DIR__ . "/../$p", __DIR__ . "/../Builder/$dir/$p");
	}

	$zip->add($b);
}

$zip->save();

echo '<h1>OK<h1>';

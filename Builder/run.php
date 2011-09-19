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
$full = isset($_GET['full']) ? true : !$isDev;

$zip = new Zipper(__DIR__ . '/Orm.zip', __DIR__, $full);

$b = new Builder(Builder::NS | Builder::NS_NETTE, $isDev);
$b->build(__DIR__ . "/../Orm", __DIR__ . "/php53/Orm");
$zip->add($b);

$b = new Builder(Builder::NONNS | Builder::NONNS_NETTE, $isDev);
$b->build(__DIR__ . "/../Orm", __DIR__ . "/php52/Orm");
$zip->add($b);

if ($full)
{
	$api = new Api;
	$api->generate(__DIR__ . "/php52/Orm", __DIR__ . "/php52/Api");
	$api->generate(__DIR__ . "/php53/Orm", __DIR__ . "/php53/Api");

	$zip->add($api);
}

$zip->save();

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
		'tests/cases',
		'tests/boot.php',
		'tests/libs/HttpPHPUnit',
		'tests/libs/dump.php',
	) as $p)
	{
		$b->build(__DIR__ . "/../$p", __DIR__ . "/$dir/$p");
	}
}

echo '<h1>OK<h1>';

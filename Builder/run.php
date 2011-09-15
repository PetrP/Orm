<?php

namespace Orm\Builder;

require_once __DIR__ . '/../tests/libs/Nette/loader.php';
require_once __DIR__ . '/../tests/libs/dump.php';
require_once __DIR__ . '/PhpParser.php';
require_once __DIR__ . '/Builder.php';
require_once __DIR__ . '/Git.php';
require_once __DIR__ . '/Zipper.php';

use Nette\Diagnostics\Debugger;

Debugger::enable();
Debugger::$strictMode = true;

set_time_limit(0);

$isDev = isset($_GET['dev']);

$zip = new Zipper(__DIR__ . '/Orm.zip', __DIR__);
if (!$isDev)
{
	$zip->addMatch(__DIR__ . '/../Orm');
}

$b = new Builder(Builder::NS | Builder::NS_NETTE, $isDev);
$b->build(__DIR__ . "/../Orm", __DIR__ . "/php53/Orm");
$zip->add($b);

$b = new Builder(Builder::NONNS | Builder::NONNS_NETTE, $isDev);
$b->build(__DIR__ . "/../Orm", __DIR__ . "/php52/Orm");
$zip->add($b);

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
		$b->build(__DIR__ . "/../$p", __DIR__ . "/../Builder/$dir/$p");
	}
}

echo '<h1>OK<h1>';

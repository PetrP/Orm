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
$b->build(__DIR__ . "/../Orm", __DIR__ . "/../Builder/s namespace/Orm");
$zip->add($b);

$b = new Builder(Builder::NONNS | Builder::NONNS_NETTE, $isDev);
$b->build(__DIR__ . "/../Orm", __DIR__ . "/../Builder/bez namespace/Orm");
$zip->add($b);

$zip->save();

foreach (array(
	Builder::NS | Builder::NS_NETTE => 's namespace/pro Nette s namespace',
	Builder::NS | Builder::NONNS_NETTE => 's namespace/pro Nette bez namespace',
	//Builder::NS | Builder::PREFIXED_NETTE => 's namespace/pro Nette s prefixy',
	Builder::NONNS | Builder::NONNS_NETTE => 'bez namespace/pro Nette bez namespace',
	Builder::NONNS | Builder::NS_NETTE => 'bez namespace/pro Nette s namespace',
	//Builder::NS | Builder::PREFIXED_NETTE => 'bez namespace/pro Nette s prefixy',
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

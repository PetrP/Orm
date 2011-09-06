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

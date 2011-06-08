<?php

namespace Orm\Builder;

require_once __DIR__ . '/../tests/libs/Nette/loader.php';
require_once __DIR__ . '/../tests/libs/dump.php';
require_once __DIR__ . '/PhpParser.php';
require_once __DIR__ . '/Builder.php';

use Nette\Diagnostics\Debugger;

Debugger::enable();
Debugger::$strictMode = true;

set_time_limit(0);

foreach (array(
	Builder::NS | Builder::NS_NETTE => 's namespace/pro Nette s namespace',
	Builder::NS | Builder::NONNS_NETTE => 's namespace/pro Nette bez namespace',
	//Builder::NS | Builder::PREFIXED_NETTE => 's namespace/pro Nette s prefixy',
	Builder::NONNS | Builder::NONNS_NETTE => 'bez namespace/pro Nette bez namespace',
	Builder::NONNS | Builder::NS_NETTE => 'bez namespace/pro Nette s namespace',
	//Builder::NS | Builder::PREFIXED_NETTE => 'bez namespace/pro Nette s prefixy',
) as $version => $dir)
{
	$b = new Builder($version);
	foreach (array(
		'Orm',
		'tests/unit',
		'tests/boot.php',
		'tests/libs/HttpPHPUnit',
		'tests/libs/dump.php',
	) as $p)
	{
		$b->build(__DIR__ . "/../$p", __DIR__ . "/../Builder/$dir/$p");
	}
}

echo '<h1>OK<h1>';

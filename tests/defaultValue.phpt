<?php

require dirname(__FILE__) . '/base.php';
TestHelpers::$oldDump = false;

/**
 * @property int $x1 {default 1}
 * @property bool $x2 {default true}
 * @property bool $x3 {default false}
 * @property string|NULL $x4 {default NULL}
 * @property string|NULL $x5 {default nazdar}
 * @property int|NULL $x6 {default 5.65}
 * @property float|NULL $x7 {default 5.65}
 * @property DateTime $x8 {default 2010-01-01}
 *
 * @property DateTime $date
 */
class Def extends Entity
{
	protected function getDefaultDate()
	{
		return Tools::createDateTime('2010-06-23 20:01:01');
	}
}


$d = new Def;

foreach ($d->toArray() as $k => $v) dt($v, $k);


__halt_compiler();
------EXPECT------
id: NULL

x1: 1

x2: TRUE

x3: FALSE

x4: NULL

x5: "nazdar"

x6: 5

x7: 5.65

x8: DateTime53(
	"date" => "2010-01-01 00:00:00"
	"timezone_type" => 3
	"timezone" => "Europe/Prague"
)

date: DateTime53(
	"date" => "2010-06-23 20:01:01"
	"timezone_type" => 3
	"timezone" => "Europe/Prague"
)

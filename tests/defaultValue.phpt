<?php

require dirname(__FILE__) . '/base.php';


/**
 * @property int $position {@default 1}
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

dump($d->position);
dump($d->date);


__halt_compiler();
------EXPECT------
int(1)

object(DateTime53) (3) {
	"date" => string(19) "2010-06-23 20:01:01"
	"timezone_type" => int(3)
	"timezone" => string(13) "Europe/Prague"
}

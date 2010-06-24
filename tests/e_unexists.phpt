<?php

require dirname(__FILE__) . '/base.php';

/**
 * @property string $name
 */
class Test extends Entity
{
	
	public function setXxx($v)
	{
		return $this->setValue('xxx', $v);
	}
	
	public function setBbb($v)
	{
		return $this->setValue('ccc', $v);
	}
}


$t = new Test;

try {
	$t->neexistuje = 'aaaaa';
} catch (Exception $e) { dump($e); }

try {
	$t->setXxx('aaaaa');
	dump('todo povolit nebo nepovolit? muze byt wtf');
} catch (Exception $e) { dump($e); }

try {
	$t->aaa = 'aaaaa';
} catch (Exception $e) { dump($e); }

try {
	$t->setBbb('aaaaa');
} catch (Exception $e) { dump($e); }

__halt_compiler();
------EXPECT------
Exception MemberAccessException: Cannot write to an undeclared property Test::$neexistuje.

Exception MemberAccessException: Cannot write to an undeclared property Test::$xxx.

Exception MemberAccessException: Cannot write to an undeclared property Test::$aaa.

Exception MemberAccessException: Cannot write to an undeclared property Test::$ccc.
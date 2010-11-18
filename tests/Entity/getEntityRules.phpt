<?php

require dirname(__FILE__) . '/../base.php';
TestHelpers::$oldDump = false;

class Test1 extends Entity
{
	static function m($en)
	{
		return self::getEntityRules($en);
	}
}

class Test4 extends Entity
{
	public static function createMetaData($entityClass)
	{
		return array();
	}
}

class Test2 extends Object
{

}

function typeof($m)
{
	if (is_bool($m)) return $m ? 'TRUE' : 'FALSE';
	return is_object($m) ? get_class($m) : gettype($m);
}

function dte($f)
{
	try {
		if (is_string($f)) eval("dt($f);");
		else dt(callback($f)->invoke());
	} catch (Exception $e) { dt($e); }
}



dte('typeof(Test1::m("Test1"))');
dte('typeof(Test1::m("Test2"))');
dte('typeof(Test1::m("Test3"))');
dte('typeof(Test1::m("Test4"))');


__halt_compiler();
------EXPECT------
"array"

Exception InvalidStateException: 'Test2' isn`t instance of IEntity

Exception InvalidStateException: Class 'Test3' doesn`t exists

Exception InvalidStateException: It`s expected that 'IEntity::createMetaData' will return 'MetaData'.

<?php

require dirname(__FILE__) . '/base.php';
TestHelpers::$oldDump = false;

/**
 * @property string $blah
 * @property -read string $oldRead
 * @property-read string $read
 */
class Test extends Entity
{
	public static function ger()
	{
		return Entity::getEntityRules('Test');
	}
}

foreach (Test::ger() as $property => $rule)
{
	dt(trim((isset($rule['get']) ? 'get' : '') . ' ' . (isset($rule['set']) ? 'set' : '')), $property);
}

__halt_compiler();
------EXPECT------
id: "get"

blah: "get set"

oldRead: "get"

read: "get"

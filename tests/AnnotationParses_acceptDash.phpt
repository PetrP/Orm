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

}

foreach (MetaData::getEntityRules('Test') as $property => $rule)
{
	dt(trim((isset($rule['get']) ? 'get' : '') . ' ' . (isset($rule['set']) ? 'set' : '')), $property);
}

__halt_compiler();
------EXPECT------
id: "get"

blah: "get set"

oldRead: "get"

read: "get"

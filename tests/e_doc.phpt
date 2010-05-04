<?php

require dirname(__FILE__) . '/base.php';

/**
 * @property $readWrite
 * @property-read $read
 * @property-write $write
 */
class Test extends Entity
{

}

$t = new Test;

$t->readWrite = 'Lorem ipsum';
$t->write = 'Ipsum lorem';
try {
	$t->read = 'Xxxxx';
} catch (Exception $e) { dump($e, 'set read'); }

dump($t->readWrite, 'get readWrite');
try {
	dump($t->write, 'get write');
} catch (Exception $e) { dump($e, 'get write'); }
dump($t->read, 'get read');


dump(Manager::getEntityParams('Test'));

__halt_compiler();
------EXPECT------
set read: Exception MemberAccessException: Cannot assign to a read-only property Test::$read.

get readWrite: string(11) "Lorem ipsum"

get write: Exception MemberAccessException: Cannot assign to a write-only property Test::$write.

get read: NULL

array(3) {
	"readWrite" => array(2) {
		"get" => array(2) {
			"method" => NULL
			"type" => string(5) "mixed"
		}
		"set" => array(2) {
			"method" => NULL
			"type" => string(5) "mixed"
		}
	}
	"read" => array(1) {
		"get" => array(2) {
			"method" => NULL
			"type" => string(5) "mixed"
		}
	}
	"write" => array(1) {
		"set" => array(2) {
			"method" => NULL
			"type" => string(5) "mixed"
		}
	}
}
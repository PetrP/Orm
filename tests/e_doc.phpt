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
} catch (Exception $e) { dt($e, 'set read'); }

dt($t->readWrite, 'get readWrite');
try {
	dt($t->write, 'get write');
} catch (Exception $e) { dt($e, 'get write'); }
dt($t->read, 'get read');


dt(EntityManager::getEntityParams('Test'));

__halt_compiler();
------EXPECT------
set read: Exception MemberAccessException: Cannot write to a read-only property Test::$read.

get readWrite: string(11) "Lorem ipsum"

get write: Exception MemberAccessException: Cannot read to a write-only property Test::$write.

get read: NULL

array(4) {
	"id" => array(%i%) {
		"types" => array(1) {
			0 => string(3) "int"
		}
		"get" => array(1) {
			"method" => NULL
		}
		"set" => NULL
		"fk" => NULL
		"since" => string(6) "Entity"
	}
	"readWrite" => array(%i%) {
		"types" => array(0)
		"get" => array(1) {
			"method" => NULL
		}
		"set" => array(1) {
			"method" => NULL
		}
		"fk" => NULL
		"since" => string(4) "Test"
	}
	"read" => array(%i%) {
		"types" => array(0)
		"get" => array(1) {
			"method" => NULL
		}
		"set" => NULL
		"fk" => NULL
		"since" => string(4) "Test"
	}
	"write" => array(%i%) {
		"types" => array(0)
		"get" => NULL
		"set" => array(1) {
			"method" => NULL
		}
		"fk" => NULL
		"since" => string(4) "Test"
	}
}

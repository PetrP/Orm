<?php

require dirname(__FILE__) . '/base.php';

/**
 * @property $readWrite
 * @property-read $read
 */
class Test extends Entity
{

}

$t = new Test;

$t->readWrite = 'Lorem ipsum';
try {
	$t->read = 'Xxxxx';
} catch (Exception $e) { dt($e, 'set read'); }

dt($t->readWrite, 'get readWrite');
dt($t->read, 'get read');


dt(EntityManager::getEntityParams('Test'));

__halt_compiler();
------EXPECT------
set read: Exception MemberAccessException: Cannot write to a read-only property Test::$read.

get readWrite: string(11) "Lorem ipsum"

get read: NULL

array(3) {
	"id" => array(%i%) {
		"types" => array(1) {
			0 => string(3) "int"
		}
		"get" => array(1) {
			"method" => string(5) "getId"
		}
		"set" => NULL
		"fk" => NULL
		"since" => string(6) "Entity"
		"relationship" => NULL
		"relationshipParam" => NULL
		"default" => NULL
	}
	"readWrite" => array(8) {
		"types" => array(0)
		"get" => array(1) {
			"method" => NULL
		}
		"set" => array(1) {
			"method" => NULL
		}
		"fk" => NULL
		"since" => string(4) "Test"
		"relationship" => NULL
		"relationshipParam" => NULL
		"default" => NULL
	}
	"read" => array(8) {
		"types" => array(0)
		"get" => array(1) {
			"method" => NULL
		}
		"set" => NULL
		"fk" => NULL
		"since" => string(4) "Test"
		"relationship" => NULL
		"relationshipParam" => NULL
		"default" => NULL
	}
}

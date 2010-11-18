<?php

require dirname(__FILE__) . '/base.php';
TestHelpers::$oldDump = false;
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


dt(AnnotationMetaData::getMetaData('Test')->toArray());

__halt_compiler();
------EXPECT------
set read: Exception MemberAccessException: Cannot write to a read-only property Test::$read.

get readWrite: "Lorem ipsum"

get read: NULL

array(
	"id" => array(
		"types" => array(
			"int"
		)
		"get" => array(
			"method" => "getId"
		)
		"set" => NULL
		"since" => "Entity"
		"relationship" => NULL
		"relationshipParam" => NULL
		"default" => NULL
		"enum" => NULL
	)
	"readWrite" => array(
		"types" => array()
		"get" => array(
			"method" => NULL
		)
		"set" => array(
			"method" => NULL
		)
		"since" => "Test"
		"relationship" => NULL
		"relationshipParam" => NULL
		"default" => NULL
		"enum" => NULL
	)
	"read" => array(
		"types" => array()
		"get" => array(
			"method" => NULL
		)
		"set" => NULL
		"since" => "Test"
		"relationship" => NULL
		"relationshipParam" => NULL
		"default" => NULL
		"enum" => NULL
	)
)

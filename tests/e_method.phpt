<?php

require dirname(__FILE__) . '/base.php';
TestHelpers::$oldDump = false;

/**
 * @property $readWrite
 * @property-read $read
 * @property $date DateTime
 */
class Test extends Entity
{
	public function setReadWrite($value)
	{
		$this->setValue('readWrite', md5($value));
	}

	public function setDate($date)
	{
		if (!($date instanceof DateTime))
		{
			$date = new DateTime53(is_numeric($date) ? date('Y-m-d H:i:s', $date) : $date);
		}
		return $this->setValue('date', $date);
	}
}

$t = new Test;

$t->readWrite = 'Lorem ipsum';
try {
	$t->read = 'Xxxxx';
} catch (Exception $e) { dt($e, 'set read'); }

dt($t->readWrite, 'get readWrite');
dt($t->read, 'get read');


$t->setReadWrite('asdasd');
dt($t->getReadWrite(), 'getReadWrite');
dt($t->readWrite, 'readWrite');
dt($t->setDate(time())->getDate(), 'getDate');


dt(AnnotationMetaData::getEntityParams('Test'));

__halt_compiler();
------EXPECT------
set read: Exception MemberAccessException: Cannot write to a read-only property Test::$read.

get readWrite: "0956d2fbd5d5c29844a4d21ed2f76e0c"

get read: NULL

getReadWrite: "a8f5f167f44f4964e6c998dee827110c"

readWrite: "a8f5f167f44f4964e6c998dee827110c"

getDate: DateTime53(
	"date" => "%i%-%i%-%i% %i%:%i%:%i%"
	"timezone_type" => 3
	"timezone" => "%a%"
)

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
			"method" => "setReadWrite"
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
	"date" => array(
		"types" => array(
			"datetime"
		)
		"get" => array(
			"method" => NULL
		)
		"set" => array(
			"method" => "setDate"
		)
		"since" => "Test"
		"relationship" => NULL
		"relationshipParam" => NULL
		"default" => NULL
		"enum" => NULL
	)
)

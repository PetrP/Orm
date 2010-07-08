<?php

require dirname(__FILE__) . '/base.php';

/**
 * @property $readWrite
 * @property-read $read
 * @property-write $write
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
$t->write = 'Ipsum lorem';
try {
	$t->read = 'Xxxxx';
} catch (Exception $e) { dump($e, 'set read'); }

dump($t->readWrite, 'get readWrite');
try {
	dump($t->write, 'get write');
} catch (Exception $e) { dump($e, 'get write'); }
dump($t->read, 'get read');


$t->setReadWrite('asdasd');
dump($t->getReadWrite(), 'getReadWrite');
dump($t->readWrite, 'readWrite');
dump($t->setDate(time())->getDate(), 'getDate');


dump(EntityManager::getEntityParams('Test'));

__halt_compiler();
------EXPECT------
set read: Exception MemberAccessException: Cannot write to a read-only property Test::$read.

get readWrite: string(32) "0956d2fbd5d5c29844a4d21ed2f76e0c"

get write: Exception MemberAccessException: Cannot read to a write-only property Test::$write.

get read: NULL

getReadWrite: string(32) "a8f5f167f44f4964e6c998dee827110c"

readWrite: string(32) "a8f5f167f44f4964e6c998dee827110c"

getDate: object(DateTime53) (3) {
	"date" => string(19) "%i%-%i%-%i% %i%:%i%:%i%"
	"timezone_type" => int(3)
	"timezone" => string(13) "Europe/Prague"
}

array(5) {
	"id" => array(3) {
		"types" => array(1) {
			0 => string(3) "int"
		}
		"get" => array(1) {
			"method" => NULL
		}
		"since" => string(6) "Entity"
	}
	"readWrite" => array(4) {
		"types" => array(0)
		"get" => array(1) {
			"method" => NULL
		}
		"since" => string(4) "Test"
		"set" => array(1) {
			"method" => string(12) "setReadWrite"
		}
	}
	"read" => array(3) {
		"types" => array(0)
		"get" => array(1) {
			"method" => NULL
		}
		"since" => string(4) "Test"
	}
	"write" => array(3) {
		"types" => array(0)
		"set" => array(1) {
			"method" => NULL
		}
		"since" => string(4) "Test"
	}
	"date" => array(4) {
		"types" => array(1) {
			0 => string(8) "datetime"
		}
		"get" => array(1) {
			"method" => NULL
		}
		"since" => string(4) "Test"
		"set" => array(1) {
			"method" => string(7) "setDate"
		}
	}
}

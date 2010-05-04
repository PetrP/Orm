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


dump(Manager::getEntityParams('Test'));

__halt_compiler();
------EXPECT------
set read: Exception MemberAccessException: Cannot assign to a read-only property Test::$read.

get readWrite: string(32) "0956d2fbd5d5c29844a4d21ed2f76e0c"

get write: Exception MemberAccessException: Cannot assign to a write-only property Test::$write.

get read: NULL

getReadWrite: string(32) "a8f5f167f44f4964e6c998dee827110c"

readWrite: string(32) "a8f5f167f44f4964e6c998dee827110c"

getDate: object(DateTime53) (3) {
	"date" => string(19) "%i%-%i%-%i% %i%:%i%:%i%"
	"timezone_type" => int(3)
	"timezone" => string(13) "Europe/Prague"
}

array(4) {
	"readWrite" => array(2) {
		"get" => array(2) {
			"method" => NULL
			"type" => string(5) "mixed"
		}
		"set" => array(2) {
			"method" => string(12) "setReadWrite"
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
	"date" => array(2) {
		"get" => array(2) {
			"method" => NULL
			"type" => string(8) "datetime"
		}
		"set" => array(2) {
			"method" => string(7) "setDate"
			"type" => string(8) "datetime"
		}
	}
}
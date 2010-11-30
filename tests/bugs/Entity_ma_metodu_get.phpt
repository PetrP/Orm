<?php

require dirname(__FILE__) . '/../base.php';

class Test extends Entity
{
	public function get()
	{
	}
}


dt(MetaData::getEntityRules('Test'));


__halt_compiler();
------EXPECT------
array(1) {
	"id" => array(8) {
		"types" => array(1) {
			0 => string(3) "int"
		}
		"get" => array(1) {
			"method" => string(5) "getId"
		}
		"set" => NULL
		"since" => string(6) "Entity"
		"relationship" => NULL
		"relationshipParam" => NULL
		"default" => NULL
		"enum" => NULL
	}
}

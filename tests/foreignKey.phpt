<?php

require dirname(__FILE__) . '/base.php';

/**
* @property-read int $id
* @property string $text
* @property Test|NULL $int
* @property string $char
* @foreignKey $int Test
*/
class Test extends Entity
{
	public function setChar($char)
	{
		if (strlen($char) !== 1)
		{
			throw new LengthException();
		}
		return $this->setValue('char', $char);
	}
	
	public function setText($text)
	{
		if (strlen($text) > 5)
		{
			$text = substr($text, 0, 5);
		}
		$this->setValue('text', $text);
	}
	
	public function setInt($i)
	{
		return $this->setValue('int', empty($i) ? NULL : $i);
	}
}

class TestRepository extends Repository
{

}

class TestMapper extends DibiMapper
{
	public function createConventional()
	{
		return new NoConventional;
	}
}

for ($i=1;$i--;)
{
	$j = rand(0, PHP_INT_MAX);
	$t = new Test();
	$m = md5($i.$j);
	$t->text = $m;
	$t->char = $m{0};
	$t->int = Model::getRepository('test')->getById(153546);
	dd(Model::getRepository('test')->persist($t));
}


/*
Model::init(array(
	'User' => 'Users',
	'Email' => 'Emails',
));*/

/*
for ($i=1000;$i--;)
	Model::getRepository('test')->getById(153548);
*/
foreach (Model::getRepository('test')->findById($t->id) as $x)
{
	dump($x->toArray());
}

__halt_compiler();
------EXPECT------
array(4) {
	"id" => int(%i%)
	"text" => string(5) "%a%"
	"int" => object(Test) (3) {
		"values" private => array(4) {
			"id" => int(153546)
			"text" => string(5) "d3eb9"
			"int" => NULL
			"char" => string(1) "a"
		}
		"valid" private => array(4) {
			"id" => bool(TRUE)
			"text" => bool(TRUE)
			"int" => bool(TRUE)
			"char" => bool(TRUE)
		}
		"rules" private => array(4) {
			"id" => array(3) {
				"types" => array(1) {
					0 => string(3) "int"
				}
				"get" => array(1) {
					"method" => NULL
				}
				"since" => string(4) "Test"
			}
			"text" => array(4) {
				"types" => array(1) {
					0 => string(6) "string"
				}
				"get" => array(1) {
					"method" => NULL
				}
				"since" => string(4) "Test"
				"set" => array(1) {
					"method" => string(7) "setText"
				}
			}
			"int" => array(5) {
				"types" => array(2) {
					0 => string(4) "test"
					1 => string(4) "null"
				}
				"get" => array(1) {
					"method" => NULL
				}
				"since" => string(4) "Test"
				"set" => array(1) {
					"method" => string(6) "setInt"
				}
				"fk" => string(4) "Test"
			}
			"char" => array(4) {
				"types" => array(1) {
					0 => string(6) "string"
				}
				"get" => array(1) {
					"method" => NULL
				}
				"since" => string(4) "Test"
				"set" => array(1) {
					"method" => string(7) "setChar"
				}
			}
		}
	}
	"char" => string(1) "%a%"
}



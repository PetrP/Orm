<?php

require dirname(__FILE__) . '/base.php';

/**
* @property-read int $id
* @property string $text
* @property Test $int
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
}

class TestRepository extends Repository
{

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

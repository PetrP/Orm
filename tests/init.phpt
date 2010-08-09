<?php

require dirname(__FILE__) . '/base.php';

/**
 * @property-read int $id
 * @property string $text
 * @property int $int
 * @property string $char
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

for ($i=0;$i--;)
{
	$j = rand(0, PHP_INT_MAX);
	$t = new Test();
	$m = md5($i.$j);
	$t->text = $m;
	$t->char = $m{0};
	$t->int = $j;
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
foreach (Model::getRepository('test')->findAll()->applyLimit(100) as $x)
{
	$x->toArray();
}
$x = Model::getRepository('test')->findAll()->applyLimit(100)->fetchAll();
foreach (Model::getRepository('test')->findByChar('a')->applyLimit(100) as $x)
{
	if ($x->char !== 'a')
	{
		dt($x->toArray());
	}
}

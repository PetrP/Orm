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
		return new NoConventional($this);
	}
}

for ($i=1;$i--;)
{
	$j = rand(0, PHP_INT_MAX);
	$t = new Test();
	$m = md5($i.$j);
	$t->text = $m;
	$t->char = $m{0};
	$t->int = $model->test->getById(153546);
	dd($model->test->persist($t));
}
$model->test->flush();


/*
for ($i=1000;$i--;)
	$model->test->getById(153548);
 */
foreach (Model::getRepository('test')->findById($t->id) as $x)
{
	dt($x->toArray(Entity::ENTITY_TO_ARRAY));
}


/**
 * @property Test $parent
 * @fk $parent Test
 */
class Test3 extends Entity
{

}
class Test3Repository extends Repository
{

}


$t3 = new Test3;
$t3->parent = $t;
$model->test3->persist($t3);
$model->test3->flush();

$t3 = $model->test3->findAll()->fetch();
dt($t3->toArray(Entity::ENTITY_TO_ID));

__halt_compiler();
------EXPECT------
array(4) {
	"id" => int(%i%)
	"text" => string(%i%) "%a%"
	"int" => array(%i%) {
		"id" => int(153546)
		"text" => string(5) "d3eb9"
		"int" => NULL
		"char" => string(1) "a"
	}
	"char" => string(%i%) "%a%"
}

array(2) {
	"id" => int(%i%)
	"parent" => int(%i%)
}

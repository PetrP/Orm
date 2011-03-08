<?php

require dirname(__FILE__) . '/base.php';
TestHelpers::$oldDump = false;

/**
 * @property string $name
 * @property ArticlesToTags $tags {m:m}
 */
class Article extends Entity
{
	public function __construct($name = NULL)
	{
		parent::__construct();
		$this->name = $name;
	}
}

/**
 * @property string $name
 */
class Tag extends Entity
{
	public function __construct($name = NULL)
	{
		parent::__construct();
		$this->name = $name;
	}
}

class ArticlesRepository extends Repository {}
class TagsRepository extends Repository {}

abstract class BaseMapper extends ArrayMapper
{
	public $data = array();
	protected function loadData()
	{
		return $this->data;
	}
	protected function saveData(array $data)
	{
		$this->data = $data;
	}
}

class ArticlesMapper extends BaseMapper {}
class TagsMapper extends BaseMapper {}

class ArticlesToTags extends ManyToMany
{
	protected function createMapper()
	{
		return new ArrayManyToManyMapper;
	}
}




$a = new Article('Clanek1');

$a->tags->add($x = new Tag('kategorie'));
$model->articles->persistAndFlush($a);
dt($model->articles->mapper->data);

$a->tags->add(new Tag('kategorie2'));
$model->articles->persistAndFlush($a);
dt($model->articles->mapper->data);

$a->tags->remove($x);
$model->articles->persistAndFlush($a);
dt($model->articles->mapper->data);

$a->tags->set(array(new Tag('kategorie3'), new Tag('kategorie4')));
$model->articles->persistAndFlush($a);
dt($model->articles->mapper->data);

__halt_compiler();
------EXPECT------
array(
	1 => array(
		"id" => 1
		"name" => "Clanek1"
		"tags" => array(
			1 => 1
		)
	)
)

array(
	1 => array(
		"id" => 1
		"name" => "Clanek1"
		"tags" => array(
			1 => 1
			2 => 2
		)
	)
)

array(
	1 => array(
		"id" => 1
		"name" => "Clanek1"
		"tags" => array(
			2 => 2
		)
	)
)

array(
	1 => array(
		"id" => 1
		"name" => "Clanek1"
		"tags" => array(
			3 => 3
			4 => 4
		)
	)
)
